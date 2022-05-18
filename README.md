# CustomBlockLoader
PocketMine-MP plugin that adds new blocks to the server!

# Reference
This plugin is experimental. <br>
We are not responsible for any problems that arise on the server by using the experimental stage.

+ [Official Documentation](https://docs.microsoft.com/en-us/minecraft/creator/reference/content/blockreference/)
+ [Customies](https://github.com/TwistedAsylumMC/Customies)

# Example Code
```php
$this->customBlockManager = CustomBlockManager::getInstance();

$info = new CustomBlockInfo("custom:test_block");
$info->addComponent(new MaterialComponent("acacia_planks", "opaque", true, true));
$this->customBlockManager->register(new CustomBlock($info->toBlockIdentifier(), 'Test Block', new BlockBreakInfo(1), $info));
```