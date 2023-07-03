<?php

namespace Ki;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};
use pocketmine\item\StringToItemParser;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use onebone\economyapi\EconomyAPI;
use pocketmine\inventory\BaseInventory;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\Position;

class FixItem extends PB implements L {

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        switch($cmd->getName()){
            case "fix":
                if(!$sender instanceof Player){
                    $sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
                    return true;
                }else{
                    $this->FixUI($sender);
                }
            break;
        }
        return true;
    }

    public function FixUI(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $sender, ?int $data = null){
        $result = $data;
        if($result === null){
            return;
            }
            switch($result){
                case 0:
                $this->Fix($sender);
                break;
            }
        }); 
        $form->setTitle("§l§6Repair");
        $form->addButton("§l§e● §cRepair §e●", 1, "https://cdn-icons-png.flaticon.com/128/1973/1973830.png");
        $form->sendToPlayer($sender);
            return $form;
    }

    public function Fix($sender){
        $slot = $sender->getInventory()->getHeldItemIndex();
        $item = $sender->getInventory()->getItem($slot);
        $cash = 1000; #cost with item no have enchantments
        $cashec = 10000; #cost with item have enchantments
        if($item instanceof \pocketmine\item\ItemBlock){
            $sender->sendMessage("§l§c● §ePlease Use Tool To Fix");
        }
        else{
            if($item->hasEnchantments()){
                if(EconomyAPI::getInstance()->myMoney($sender) >= $cashec){
                    EconomyAPI::getInstance()->reduceMoney($sender, $cashec);
                    $sender->getInventory()->setItem($slot, $item->setDamage(0));
                    $sender->sendMessage("§l§c● §eYou Repaired Your Item In Hand With Price ". $cashec ." Money");
                    $position = $sender->getPosition();
       		 		$packet = new PlaySoundPacket();
        			$packet->soundName = "random.anvil_use";
        			$packet->x = $position->getX();
       				$packet->y = $position->getY();
        			$packet->z = $position->getZ();
        			$packet->volume = 1;
        			$packet->pitch = 1;
        			$sender->getNetworkSession()->sendDataPacket($packet);
                }
                else{
                    $sender->sendMessage("§l§c● §eYou Doesn't Have Enough Money To Fix");
                }
            }
            else{
                if(EconomyAPI::getInstance()->myMoney($sender) >= $cash){
                    EconomyAPI::getInstance()->reduceMoney($sender, $cash);
                    $sender->getInventory()->setItem($slot, $item->setDamage(0));
                    $sender->sendMessage("§l§c● §eYou Repaired Your Item In Hand With Price ". $cash ." Money");
                    $position = $sender->getPosition();
       		 		$packet = new PlaySoundPacket();
        			$packet->soundName = "random.anvil_use";
        			$packet->x = $position->getX();
       				$packet->y = $position->getY();
        			$packet->z = $position->getZ();
        			$packet->volume = 1;
        			$packet->pitch = 1;
        			$sender->getNetworkSession()->sendDataPacket($packet);
                }
                else{
                    $sender->sendMessage("§l§c● §eYou Doesn't Have Enough Money To Fix");
                }
            }
        }
    }
}