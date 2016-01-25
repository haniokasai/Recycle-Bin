<?php

namespace NoLostItem;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\Permission;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;

class NoLostItem extends PluginBase implements Listener{

	public function onEnable(){
	
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$task = new CallbackTask(array($this, "schedule"));
		$time = 400;
#		$this->getServer()->getScheduler()->scheduleRepeatingTask($task, $time);
		$dbf = $this->getDataFolder()."item.sqlite3.db";
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);
		}
		if(!file_exists($dbf)){
			$this->db = new \SQLite3($dbf, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		}else{
			$this->db = new \SQLite3($dbf, SQLITE3_OPEN_READWRITE);
		}
		$this->db->query("CREATE TABLE IF NOT EXISTS itemp (name TEXT PRIMARY KEY, id TEXT)");
		$this->getLogger()->info(TextFormat::GREEN."sqlite3 was loaded!");
		
		
	}

	public function onDisable(){
		$this->schedule();
		$this->db->close();
		$this->getLogger()->info(TextFormat::GREEN."sqlite3 was unloaded!");
	}
	public function joinplayer(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$result = $this->db->query("SELECT name,id FROM itemp WHERE name = \"$name\"");
		$rows = $result->fetchArray(SQLITE3_ASSOC);

		if($result){ 
				if($rows['name'] != null and $rows['id'] != null){
					$stack =unserialize(base64_decode($rows['id']));
					$i =1;$ok =1;
#var_dump($stack);
	
#$player->getInventory()->setArmorContents(unserialize(base64_decode($rows['armor'])));


$item =  @Item::get($stack[$i]["id"],$stack[$i]["da"],$stack[$i]["co"]);
if(@!$player->getInventory()->contains($item)){
			$player->getInventory()->clearall();
while($i <=36 and $ok == 1){
	
if(isset($stack[$i]["id"])){
	$item =  Item::get($stack[$i]["id"],$stack[$i]["da"],$stack[$i]["co"]);
/*	if(@$stack[$i]["cu"] != null){
$this->getServer()->dispatchCommand(new ConsoleCommandSender(), ""."give ".$name.
" ".$stack[$i]["id"].
":".$stack[$i]["da"].
" ".$stack[$i]["co"].
"{display:{Name:\"".
$stack[$i]["cu"]."\"}}"."");}*/
$player->getInventory()->addItem($item);
$i++;
}else{
$ok =0;
}
}
}


					
					
			}}
	}
	
	public function chan (EntityInventoryChangeEvent	 $event){
	#	$this->dare($event->getEntity());

	
	}
	
		public function quitgo (PlayerquitEvent $event){
		$this->dare($event->getPlayer());
	
	}
		public function schedule (){
		foreach($this->getServer()->getOnlinePlayers() as $player){
		$this->dare($player);

		
	
	}		}
		public function dare ($player){
			
		$name = $player->getName();
		$this->db->query("DELETE FROM itemp WHERE name = \"$name\"");
		$stack =array();
$i = 1;
		foreach($player->getInventory()->getContents() as $items){
		$stack[$i]["id"] = $items->getId();$stack[$i]["da"] = $items->getDamage();$stack[$i]["co"] = $items->getCount(); $stack[$i]["cu"] = $items->getCustomName(); 
		$i++;
		
							}


		
							
	
#var_dump($stack);

$id = base64_encode(serialize($stack));

		$this->db->query("INSERT OR REPLACE INTO itemp VALUES(\"$name\", \"$id\")");
	
	}
}
