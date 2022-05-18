<?php

declare(strict_types=1);

namespace skh6075\customblockloader;

use Closure;
use InvalidArgumentException;
use kim\present\utils\identifier\IdentifierUtils;
use OutOfRangeException;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\data\bedrock\LegacyBlockIdToStringIdMap;
use pocketmine\data\bedrock\LegacyToStringBidirectionalIdMap;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\convert\R12ToCurrentBlockMapEntry;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ReflectionClass;
use RuntimeException;
use skh6075\customblockloader\block\CustomBlock;
use skh6075\customblockloader\block\CustomBlockPalette;
use skh6075\customblockloader\task\ReallocateRuntimeBlockAsyncTask;
use SplFixedArray;
use const pocketmine\BEDROCK_DATA_PATH;

final class CustomBlockManager{
	use SingletonTrait;

	public const BLOCK_MAP_SIZE = 2048 << Block::INTERNAL_METADATA_BITS;

	public static function getInstance() : CustomBlockManager{
		return self::$instance ??= new self;
	}

	private BlockFactory $blockFactory;

	private CustomBlockPalette $blockPalette;

	/**
	 * @phpstan-var array<string, CustomBlock>
	 * @var CustomBlock[]
	 */
	private array $customBlocks = [];

	public function __construct(){
		$this->blockFactory = BlockFactory::getInstance();
		$this->blockPalette = CustomBlockPalette::getInstance();

		Closure::bind( //HACK: Closure bind hack to access inaccessible members
			closure: static function(BlockFactory $factory) : void{
				$factory->fullList->setSize(CustomBlockManager::BLOCK_MAP_SIZE);
				$factory->mappedStateIds->setSize(CustomBlockManager::BLOCK_MAP_SIZE);
				$factory->light = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 0));
				$factory->lightFilter = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 1));
				$factory->blocksDirectSkyLight = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, false));
				$factory->blastResistance = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 0.0));
			},
			newThis: null,
			newScope: BlockFactory::class
		)($this->blockFactory);
	}

	public function registerCustomRuntimeMappings(): void {
		$instance = RuntimeBlockMapping::getInstance();
		$runtimeBlockMapping = new ReflectionClass($instance);
		foreach(["legacyToRuntimeMap", "runtimeToLegacyMap"] as $propertyName){
			$property = $runtimeBlockMapping->getProperty($propertyName);
			$property->setAccessible(true);
			$property->setValue($instance, []);
		}

		$registerMappingMethod = $runtimeBlockMapping->getMethod("registerMapping");
		$registerMappingMethod->setAccessible(true);
		$registerMapping = $registerMappingMethod->getClosure($instance);
		if($registerMapping === null) {
			throw new RuntimeException("Unable to access mapping registration");
		}

		$legacyIdMap = LegacyBlockIdToStringIdMap::getInstance();
		/** @var R12ToCurrentBlockMapEntry[] $legacyStateMap */
		$legacyStateMap = [];

		$legacyStateMapReader = PacketSerializer::decoder(file_get_contents(BEDROCK_DATA_PATH . "r12_to_current_block_map.bin"), 0, new PacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary()));
		$nbtReader = new NetworkNbtSerializer();
		while(!$legacyStateMapReader->feof()){
			$id = $legacyStateMapReader->getString();
			$meta = $legacyStateMapReader->getLShort();

			$offset = $legacyStateMapReader->getOffset();
			$state = $nbtReader->read($legacyStateMapReader->getBuffer(), $offset)->mustGetCompoundTag();
			$legacyStateMapReader->setOffset($offset);
			$legacyStateMap[] = new R12ToCurrentBlockMapEntry($id, $meta, $state);
		}
		foreach($this->blockPalette->getCustomStates() as $state){
			$legacyStateMap[] = new R12ToCurrentBlockMapEntry($state->getString("name"), 0, $state);
		}

		/**
		 * @var int[][] $idToStatesMap string id -> int[] list of candidate state indices
		 */
		$idToStatesMap = [];
		$states = $this->blockPalette->getOriginStates();
		foreach($states as $k => $state){
			$idToStatesMap[$state->getString("name")][] = $k;
		}

		foreach($legacyStateMap as $pair){
			$id = $legacyIdMap->stringToLegacy($pair->getId());
			if($id === null) {
				throw new RuntimeException("No legacy ID matches " . $pair->getId());
			}
			$data = $pair->getMeta();
			if($data > 15) {
				continue;
			}
			$mappedState = $pair->getBlockState();
			$mappedName = $mappedState->getString("name");
			if(!isset($idToStatesMap[$mappedName])) {
				continue;
			}
			foreach($idToStatesMap[$mappedName] as $k){
				$networkState = $states[$k];
				if($mappedState->equals($networkState)) {
					$registerMapping($k, $id, $data);
					continue 2;
				}
			}
		}
	}

	public function getNextAvailableId(): int {
		$availableId = 1000 + count($this->customBlocks);
		if($availableId > (self::BLOCK_MAP_SIZE / 16)){
			throw new OutOfRangeException("All custom block ids are used up");
		}
		return $availableId;
	}

	public function register(CustomBlock $customBlock) : void{
		$info = $customBlock->getCustomBlockInfo();
		if($this->blockFactory->isRegistered($customBlock->getId(), $customBlock->getMeta())){
			throw new InvalidArgumentException("Block with ID " . $customBlock->getId() . " is already registered");
		}

		$this->blockFactory->register($customBlock);
		IdentifierUtils::registerItem($info->getStringId(), $customBlock->getId());
		$this->blockPalette->registerState($info->getBlockState());

		$this->customBlocks[$info->getStringId()] = $customBlock;


		Closure::bind( //HACK: Closure bind hack to access inaccessible members
			closure: static function(LegacyToStringBidirectionalIdMap $map, CustomBlock $customBlock) : void{
				$map->legacyToString[$customBlock->getId()] = $customBlock->getCustomBlockInfo()->getStringId();
				$map->stringToLegacy[$customBlock->getCustomBlockInfo()->getStringId()] = $customBlock->getId();
			},
			newThis: null,
			newScope: LegacyToStringBidirectionalIdMap::class
		)(LegacyBlockIdToStringIdMap::getInstance(), $customBlock);
	}

	public function get(string $stringId): ?CustomBlock{
		return $this->customBlocks[$stringId] ?? null;
	}

	public function addWorkerInitHook(): void {
		$blocks = serialize($this->customBlocks);
		$server = Server::getInstance();
		$server->getAsyncPool()->addWorkerStartHook(static function (int $worker) use ($server, $blocks): void {
			$server->getAsyncPool()->submitTaskToWorker(new ReallocateRuntimeBlockAsyncTask($blocks), $worker);
		});
	}

	/** @return BlockPaletteEntry[] */
	public function getBlockPalettes(): array{
		return array_map(static fn(CustomBlock $block): BlockPaletteEntry => $block->getCustomBlockInfo()->toBlockPaletteEntry(), $this->customBlocks);
	}
}