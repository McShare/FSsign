<?php

namespace FransicSteve;


use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\level\Level;


class Main extends PluginBase implements Listener
{
	public function onEnable()
	{

		$this->getServer()->getPluginManager()->registerEvents($this,$this);

  $this->getLogger()->info("FSsign正在加载中…");
  
		@mkdir($this->getDataFolder(),0777,true);
		$this->player = new Config($this->getDataFolder()."white-list.yml",Config::YAML,array(
		"白名单功能"=>"开",
		"white-list"=>[]
		));
		
		$this->config = $this->player;
		$this->sign = new Config($this->getDataFolder()."sign.yml",Config::YAML,array());
  $this->p = "§a[§bFS命令木牌系统§a]§e";
  
  $this->getLogger()->info("FSsign加载完成！");
  
}


public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
  if ($command->getName() == "FSsign") {
      if(count($args) === 0):
     return false;
     endif;
   if ($args[0] == "help") {
  $sender->sendMessage("§c==========");
  $sender->sendMessage("§a>/FSsign help 本插件的帮助");
  $sender->sendMessage("§a>/FSsign 白名单 开关白名单功能");
  $sender->sendMessage("§a>/FSsign 添加白名单 <玩家ID> 添加一个白名单");
  $sender->sendMessage("§a>/FSsign 删除白名单 <玩家ID> 删除一个白名单");
  $sender->sendMessage("§c==========");
  $sender->sendMessage("§e木牌创建格式为");
  $sender->sendMessage("§b第一行，fsc，第二行，木牌说明，第三行，命令(不加/)，第四行，空");
  $sender->sendMessage("§c==========");
    }else if ($args[0] == "白名单") {
    if ($this->config->get("白名单功能")=="关") {
     $this->config->set("白名单功能","开");
     $this->config->save();
     $sender->sendMessage($this->p."白名单功能已开！");
     } else {
     $this->config->set("白名单功能","关");
     $this->config->save();
     $sender->sendMessage($this->p."白名单功能已关！");
    }
   }else if ($args[0] == "添加白名单") {
     $list = $this->player->get("white-list");
     $list[] = $args[1];
     $this->player->set("white-list",$list);
     $this->player->save();
     $sender->sendMessage($this->p."成功将 $args[1] 添加至白名单！");
      }
            else if ($args[0] == "删除白名单") {
      
    $list = $this->player->get("white-list");
    $search = array_search($args[1],$list);
    $search = array_splice($list,$search,1);
    $this->player->set("white-list",$list);
    $this->player->save();
    $sender->sendMessage($this->p."成功将 $args[1] 移除白名单");
   }
 else{
      $sender->sendMessage("§a用法:§b/FSsign help 查看帮助");
      }
      return true;
      
      }
      
 }
 
 
 

public function change(SignChangeEvent $event)
     {
     
 $player = $event->getPlayer();
 $get1 = $event->getLine(0);
 if ($get1 == "fsc") {
   if ($player->isOp()) {
   $get2 = $event->getLine(1);
   $get3 = $event->getLine(2);
   $event->setLine(0,"§e[§bFS命令木牌§e]");
   $event->setLine(1,"§7$get2");
   $event->setLine(2,"§c点我运行命令");
   $event->setLine(3,"§9命令:§d/$get3");
   $block = $event->getBlock();
   $x = $block->getX();
   $y = $block->getY();
   $z = $block->getZ();
   $info = $x.":".$y.":".$z.":".$player->getLevel()->getFolderName();
   $this->sign->set($info,$get3);
   $this->sign->save();
   $player->sendMessage($this->p."成功创建命令木牌！");
   } else {
   $event->setLine(0,"§c木牌已失效");
   $event->setLine(1,"==========");
   $event->setLine(2,"§c$player->getName() 尝试创建木牌");
   $player->sendMessage($this->p."§e你没有权限创建木牌！");
   }
  }
}
   
   
   
   
   
public function onbreak(BlockBreakEvent $event)
    {
 
     $player = $event->getPlayer();
  $name = $player->getName();
  $block = $event->getBlock();
  $id = $block->getID();

     if ($id == 323 or $id == 63 or $id == 68) {
  $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
   $k = $sign->getText(); 

     $x = $block->getX();
     $y = $block->getY();
     $z = $block->getZ();
     $info = $x.":".$y.":".$z.":".$player->getLevel()->getFolderName();

   if ($k[0] == "§e[§bFS命令木牌§e]" AND $this->sign->get($info) != null) {

         if ($player->isOp()) {
        $x = $block->getX();
     $y = $block->getY();
     $z = $block->getZ();
     $info = $x.":".$y.":".$z.":".$player->getLevel()->getFolderName();

  $this->sign->remove($info);
  $this->sign->save();
  $player->sendMessage("§b成功移除命令木牌！");
  } else {
  $player->sendMessage("§c您不能移除命令木牌！");
  $event->setCancelled();
          }
     }
  }
}
 


   
   
   
public function interact(PlayerInteractEvent $event)
  {
 
  $player = $event->getPlayer();
  $name = $player->getName();
  $block = $event->getBlock();
  $id = $block->getID();
    if ($id == 323 or $id == 63 or $id == 68) {
  $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
   $k = $sign->getText(); 


     $x = $block->getX();
     $y = $block->getY();
     $z = $block->getZ();
     $info = $x.":".$y.":".$z.":".$player->getLevel()->getFolderName();

   if ($k[0] == "§e[§bFS命令木牌§e]" AND $this->sign->get($info) != null) {


        if ($this->player->get("白名单功能") == "开") {
          if (in_array($name,$this->player->get("white-list"))) {
   $this->getServer()->dispatchCommand($player,$this->sign->get($info));
           } else {
            $player->sendMessage($this->p."您不在白名单内，无法使用本木牌！");
               }
            } else {
         $this->getServer()->dispatchCommand($player,$this->sign->get($info));
              }
         }
      }
   }
  
}


     
      

