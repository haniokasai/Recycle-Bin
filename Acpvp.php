<?php

namespace honeypvp;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\Arrow;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\level\Position;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\level\Explosion;
use pocketmine\tile\Tile;
use pocketmine\event\Event\getEventName;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\IPlayer;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\scheduler\PluginTask;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\entity\Snowball;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\entity\Entity;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\level\format\FullChunk;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\NBT;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\UseItemPacket;
use pocketmine\network\protocol\SetEntityLinkPacket;

class honeypvp extends PluginBase implements Listener{
		
		public function onEnable(){
			
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
								$this->buy =array();
		$this->team = [1 => [] , 2 => [] ];
		$this->joinedpvp = array();
		$this->teamcore =array();
		$this->teamc =array();
		$this->hapi= $this->getServer()->getPluginManager()->getPlugin("hapi");
		$this->getServer()->loadLevel("world");	
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);
		}
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->conf = new config($this->getDataFolder() . "config.yml", config::YAML, array(
			"↓libreの設定ファイル"=> null,
			"killexp" => 1,	
			"corehp" => 10,	
			));
		}else{
			$this->conf = new config($this->getDataFolder() . "config.yml", config::YAML, array());
		}
		if(!file_exists($this->getDataFolder() . "shop.yml")){
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array(
			"↓effect cost(per second)"=> null,
			"1" => 5,
			"2" => 5,
			"3" => 5,
			"4" => 5,
			
			));
		}else{
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array());
		}
			
				$this->teamcore[1] =$this->conf->get("corehp");
				$this->teamcore[2] =$this->conf->get("corehp");
				
				
		}
	 public function onDisable(){
        unset($this->players);
		unset($this->joined);
		unset($this->team);
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$name = $p->getName();
				global ${"joined_".$name};
				$p->kick("鯖がリロードまたは終了しました。");

		}
#$level =$this->getServer()->getDefaultLevel();
#$this->getServer()->unloadLevel($level);
		system("rm -rf /ramdisk/wall/worlds/world");
system("rm -rf /ramdisk/wall/players");
		system("cp -r /ramdisk/wall/bp/worlds/world /ramdisk/wall/worlds");
        $this->getLogger()->info(TextFormat::RED ."終了しました。");
	unset($this);
    }
	
public function onCommand(CommandSender $sender, Command $command,$label,array $args){
	switch($command->getName()){
	
		case "pvp":{
			$name = $sender->getName();	
			echo 1;	
		if(isset($this->joinedpvp[$name] )){
			echo 2;	
			if($this->joinedpvp[$name] == 1 ){
				$sender->sendMessage(TextFormat::RED ."[WallPVP]あなたはすでにpvpに参加しています。");
				break;}
		}
		
		
		if(time() - @$this->teamc[1][$name] <= 30*1){
			
			  
			 echo "a2";
			$this->team[1][$name] = 1;
				$teamname = "S";
				$tcolor = TextFormat::LIGHT_PURPLE ;
			
				$pos1 = new Position(73,72,132);//座標を指定
			
				$sender->sendMessage(TextFormat::LIGHT_PURPLE  ."[WallPVP]あなたは".$teamname."チームです。");
			 
		}elseif(time() - @$this->teamc[2][$name] <= 30*1){
			echo "b1";
			
				$this->team[2][$name] = 1;
				$teamname = "M";
				$tcolor = TextFormat::AQUA ;
				$pos1 = new Position(64,76,28);//座標を指定
			
				$sender->sendMessage(TextFormat::AQUA ."[WallPVP]あなたは".$teamname."チームです。");
		}else{
			echo 3;	
			$this->joinedpvp[$name] = 1;
			if(count($this->team[1]) <= count($this->team[2])){
		 		$this->team[1][$name] = 1;
				$teamname = "S";
				$tcolor = TextFormat::LIGHT_PURPLE ;
			
				$pos1 = new Position(73,72,132);//座標を指定
			
				$sender->sendMessage(TextFormat::LIGHT_PURPLE  ."[WallPVP]あなたは".$teamname."チームです。");
			}else{
		 		$this->team[2][$name] = 1;
				$teamname = "M";
				$tcolor = TextFormat::AQUA ;
				$pos1 = new Position(64,76,28);//座標を指定
			
				$sender->sendMessage(TextFormat::AQUA ."[WallPVP]あなたは".$teamname."チームです。");
				
			}	}

				if(isset($this->team[1][$name])){
					$teamname = "[S]";
					$tcolor = TextFormat::LIGHT_PURPLE ;
				}elseif(isset($this->team[2][$name])){
					$teamname = "[M]";
					$tcolor = TextFormat::AQUA ;
				}else{
				$teamname=null; $tcolor = null;
				}
				
				
	
				if($sender->isOp()){
					$op ="[OP]";
				}else{
					$op = null;
				}
$sender->setDisPlayName($tcolor.$op.$teamname.$name. "[" .$this->hapi->getkill($name). "kill]");	
$sender->setNameTag($tcolor.$op.$teamname.$name. "[" .$this->hapi->getkill($name). "kill]");	
		

			
			$sender->teleport($pos1);
			$effect = Effect::getEffect(11); //Effect ID
			$effect->setVisible(true); //Particles
			$effect->setAmplifier(1000);
			$effect->setDuration(100); //Ticks
			$sender->addEffect($effect);
		
		
			break;
		}
	
	case "rekit":{
		$this->setKit($sender);
		break;
	}
	
	case "ok":{
			$cfg = $this->settings->getAll();
			$name = $sender->getName();
			if(isset($this->buy[$name])){
			if(time()-$this->buy[$name]["time"] <=20){
				$amount = $cfg[$this->buy[$name]["id"]];
		if($this->hapi->getkit1($name) ==  $this->buy[$name]["id"]){
					$sender->sendMessage(TextFormat::RED ."[WallPVP]購入済みです。");
				}else{
			if($this->hapi->useexp($name,$amount)){

			$this->hapi->setkit1($name,$this->buy[$name]["id"]);
						$this->setKit($sender);
			$sender->sendMessage(TextFormat::RED ."[WallPVP]".$amount."EXPの".$this->buy[$name]["id"]."を購入しました。"); 
			}else{
			$sender->sendMessage(TextFormat::RED ."[WallPVP]EXPが足りません。");
			}}
			}}
		break;
	}

	case "buy":{
		$name = $sender->getName();	
		if(!isset($args[0])){
									$sender->sendMessage("Usege: /buy '[effectID]'");
									break;
								}
								if(!ctype_digit($args[0])){
									$sender->sendMessage("Usege: /buy '[effectID]'");
									break;
								}
								$cfg = $this->settings->getAll();
								$ecost = $cfg[$args[0]];
								$sender->sendMessage("ID ".$args[0].": 値段$".$ecost."を買うのなら/ok");
								if(isset($this->buy[$name])){
									unset($this->buy[$name]);}
								$this->buy[$name]["time"] = time();
								$this->buy[$name]["id"] = $args[0];
								unset($cfg);
								unset($ecost);

		break;
	}
	case "shop":{
		$cfg = $this->settings->getAll();
								if(!isset($args[0])){
									$sender->sendMessage("Usege: /shop [effectID] [price]");
									break;
								}
								if(!ctype_digit($args[0])){
									$sender->sendMessage("Usege: /shop '[effectID]' [price]");
									break;
								}
								if(!isset($args[1])){
									$sender->sendMessage("Usege: /shop [effectID] '[price]'");
									break;
								}
								if(!$this->settings->exists($args[1])){
									$sender->sendMessage("EffectID is wrong.");
									break;
								}
								if(!ctype_digit($args[1])){
									$sender->sendMessage("Usege: /shop [effectID] '[price]'");
									break;
								}
									$this->settings->set($args[0], $args[1]);
									$this->settings->save();
									$sender->sendMessage("You changed EffectID ".$args[0]." 's price $".$args[1]);	
									$this->settings->reload() ;
									break;

								
    				        break;
	}
		case "c":{
		$name = $sender->getName();
						$players = Server::getInstance()->getOnlinePlayers();	
				if(isset($this->team[1][$name])){

				foreach ($players as $player) {
					if(isset($this->team[1][$player->getName()])){
					$player->sendMessage(TextFormat::YELLOW."[SチームC]".$name."|".$args);}
			}
				}elseif(isset($this->team[2][$name])){
									foreach ($players as $player) {
					if(isset($this->team[2][$player->getName()])){
					$player->sendMessage(TextFormat::YELLOW."[MチームC]".$name."|".$args);}
			}
				}
			
			break;}
	case "mi":{
		$name = $sender->getName();	
				if(isset($this->team[1][$name])){
					$teamname = "S";
				}elseif(isset($this->team[2][$name])){
					$teamname = "M";
				}

$name = $sender->getName();	
		$sender->sendMessage(TextFormat::RED ."//////".$name."さんの情報/////");
		if(isset($teamname)){
		$sender->sendMessage(TextFormat::RED ."[チーム] ".$teamname."チーム");}
#		$sender->sendMessage(TextFormat::RED ."[KIT] ".$this->hapi->getkit1($name));
		$sender->sendMessage(TextFormat::RED ."[レベル] ".$this->getServer()->getPluginManager()->getPlugin("hapi")->getlevel($name));
		$sender->sendMessage(TextFormat::RED ."[EXP]　".$this->getServer()->getPluginManager()->getPlugin("hapi")->getexp($name));
		$sender->sendMessage(TextFormat::RED ."[キル数] ".$this->getServer()->getPluginManager()->getPlugin("hapi")->getkill($name)."回");
		$sender->sendMessage(TextFormat::RED ."[連続キル数] ".$this->getServer()->getPluginManager()->getPlugin("hapi")->getrenkill($name)."回");
		if($this->getServer()->getPluginManager()->getPlugin("hapi")->getexp($name) !=0and$this->getServer()->getPluginManager()->getPlugin("hapi")->getdes($name)!=0){
				$sender->sendMessage(TextFormat::RED ."[キルレート] ".$this->getServer()->getPluginManager()->getPlugin("hapi")->getkill($name)/$this->getServer()->getPluginManager()->getPlugin("hapi")->getdes($name));}
		$sender->sendMessage(TextFormat::RED ."/////////////////////////////");
		break;
	}
		case "stat":{
		$sender->sendMessage(TextFormat::RED ."[WallPVP]各チームの人数です。");
		$sender->sendMessage(TextFormat::RED ."[S]".count($this->team[1])."人、[コア]".$this->teamcore[1]."HP");
		$sender->sendMessage(TextFormat::RED ."[M]".count($this->team[2])."人、[コア]".$this->teamcore[2]."HP");
	}
		
}
	
	
}

/////死んだときイベント////
	public function onPlayerDeath(PlayerDeathEvent $event){
		$name = $event->getEntity()->getName();	
$player =$event->getEntity();
	        $ev = $event->getEntity()->getLastDamageCause();
		unset($this->joinedpvp[$name]);
		$this->hapi->adddes($name);
		
		
		////unix///
		if(isset($this->team[1][$name])){
			$this->teamc[1][$name] =  time();
		}elseif(isset($this->team[2][$name])){
			$this->teamc[2][$name] =  time();
		}
		//////////
		
		
		unset($this->team[1][$name]);
		unset($this->team[2][$name]);
		unset($this->joinedpvp[$name]);
						
				
				
	
				if($player->isOp()){
					$op ="[OP]";
				}else{
					$op = null;
				}
$player->setDisPlayName($op.$name. "[" .$this->hapi->getkill($name). "kill]");	
$player->setNameTag($op.$name. "[" .$this->hapi->getkill($name). "kill]");	
	
        
		if ($ev instanceof EntityDamageByEntityEvent) {
            		$killer = $ev->getDamager();
			$killername = $killer->getName();
   		        if ($killer instanceof Player) {	
				$this->conf->reload;
            			$killer->sendMessage(""."[WallPVP]".$this->conf->get("killexp")."EXP得ました。"."");
				
				unset($this->joinedpvp[$name]);
				$dead = $event->getEntity();

				$this->hapi->addkill($killername);
				
				$this->hapi->addexp($killername, $this->conf->get("killexp")); 
				
				


				if(isset($this->team[1][$killername])){
					$teamname = "[S]";
					$tcolor = TextFormat::LIGHT_PURPLE ;
				}elseif(isset($this->team[2][$killername])){
					$teamname = "[M]";
					$tcolor = TextFormat::AQUA ;
				}else{
				$teamname=null; $tcolor = null;
				}
				
				
	
				if($killer->isOp()){
					$op ="[OP]";
				}else{
					$op = null;
				}
$killer->setDisPlayName($tcolor.$op.$teamname.$killername. "[" .$this->hapi->getkill($killername). "kill]");	
$killer->setNameTag($tcolor.$op.$teamname.$killername. "[" .$this->hapi->getkill($killername). "kill]");	
	
		
				}
			}
				
		}
		
	public function onSignChange(SignChangeEvent $sc){

		if(!$sc->getPlayer()->isOP() && $sc->getLine(0) == "[BUY]"){

			$sc->setCancelled();
			$sc->getPlayer()->sendMessage("あなたはopではありません。");

		}
	}

////看板
public function asle(PlayerInteractEvent $event){
	 $name = $event->getPlayer()->getName();
	 $player = $event->getPlayer();
	 if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68){
            $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            if(!($sign instanceof Sign)){
                return;
            }
            $sign = $sign->getText();
			if($sign[0]=='[BUY]'){
				$p = $sign[1];
				$itemid = $sign[2];
				$amount = $sign[3];

			if($this->hapi->useexp($name,$p)){
$item = Item::get($itemid, 1, $amount);//itemオブジェクトの生成
$player->getInventory()->addItem($item);//アイテムを追加

			$player->sendMessage(TextFormat::RED ."[WallPVP]".$p."EXPの".$itemid."を購入しました。"); 
			}else{
			$player->sendMessage(TextFormat::RED ."[WallPVP]EXPが足りません。");
			}}
			}
				
	 }


//////
	
	
	
/////おしまい☆
public function onPlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$player->setNameTag("");
		$name = $player->getName();
		
		
				////unix///
		if(isset($this->team[1][$name])){
			$this->teamc[1][$name] =  time();
		}elseif(isset($this->team[2][$name])){
			$this->teamc[2][$name] =  time();
		}
		//////////
		
		
		unset($this->team[1][$name]);
		unset($this->team[2][$name]);
		unset($this->joinedpvp[$name]);
}
///おしまい☆

////共食い
public function optionbow(EntityDamageEvent $event){
		if($event instanceof EntityDamageEvent){
			if($event instanceof EntityDamageByEntityEvent) {
			if($event->getDamager() instanceof Player && $event->getEntity() instanceof Player) {
				$sender = $event->getDamager();
				$reciever = $event->getEntity();
				unset($sname);
				unset($rname);
				$sname = $sender->getName();
				$rname = $reciever->getName();
				
					if(isset($this->team[1][$sname])and  isset($this->team[1][$rname])){$sender->sendPopUp(TextFormat::YELLOW."同チームのプレイヤーは攻撃できません。");
						$event->setCancelled();
                    }
elseif(isset($this->team[2][$sname])and isset( $this->team[2][$rname])){$sender->sendPopUp(TextFormat::YELLOW."同チームのプレイヤーは攻撃できません。");
						$event->setCancelled();
					
					}
				
			}
			
			}
		}

		}
		
////おしまい♡




	public function setKit($player){
		$name = $player->getName();
		$kitid = $this->hapi->getkit1($name);
		#$player->getInventory()->clearall();
$i =15;
while($i >=0){
$item = Item::get(351,$i,64);
$player->getInventory()->removeItem($item);
$i--;
}
$item = Item::get(345,1,64);
$player->getInventory()->removeItem($item);
$item = Item::get(346,1,64);
$player->getInventory()->removeItem($item);
$item = Item::get(344,1,64);
$player->getInventory()->removeItem($item);

		$player->removeAllEffects();
	if($kitid == 1){//ok
$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:1 1 {display:{Name:\"§r§6§lAirhook Lv1\"}}");
	}elseif($kitid == 2){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:2 1 {display:{Name:\"§r§6§lAirhook Lv2\"}}");
	}elseif($kitid == 3){
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:3 1 {display:{Name:\"§r§6§lAroundFire\"}}");
	}elseif($kitid == 4){
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:4 1 {display:{Name:\"§r§6§l???\"}}");	
	}elseif($kitid == 5){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:5 1 {display:{Name:\"§r§6§lHookShotLv1\"}}");
	}elseif($kitid == 6){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:6 1 {display:{Name:\"§r§6§lHookShotLv2\"}}");
	}elseif($kitid == 7){//isviwer Weakness
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:7 1 {display:{Name:\"§r§6§lAroundPoison\"}}");
}elseif($kitid == 8){//efect 15  blinding
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:8 1 {display:{Name:\"§r§6§lAroundBlinding\"}}");
}elseif($kitid == 9){//isviewer tukauttozibunmo damage
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:9 1 {display:{Name:\"§r§6§lAroundAtt@cker\"}}");
}elseif($kitid == 10){//isviewer　KNOCKBACK
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:10 1 {display:{Name:\"§r§6§lAroundKnockB@cker\"}}");
}elseif($kitid == 11){//isviewer　Nausea
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:11 1 {display:{Name:\"§r§6§lAroundNausea\"}}");
}elseif($kitid == 12){//isviewer　MiningFatigue
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 351:12 1 {display:{Name:\"§r§6§lAroundMiningFatigue	\"}}");

	}elseif($kitid == 40){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 345 1 {display:{Name:\"§r§6§lEasyHookShot\"}}");
		}elseif($kitid == 41){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 347 1 {display:{Name:\"§r§6§lEasyJetPack\"}}");
		}elseif($kitid == 42){//ok
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "give ".$name." 346 1 {display:{Name:\"§r§6§l
Machine gun\"}}");

}elseif($kitid == 30){//efect 16 Night-sight //OK
$player->addEffect(Effect::getEffect(16)->setDuration(10000*20)->setAmplifier(2)->setVisible(true));	
}elseif($kitid == 31){//efect 4 attack //OK
$player->addEffect(Effect::getEffect(4)->setDuration(10000*20)->setAmplifier(3)->setVisible(true));	
}elseif($kitid == 32){//efect 8 jumper //OK
$player->addEffect(Effect::getEffect(8)->setDuration(10000*20)->setAmplifier(4)->setVisible(true));	
}elseif($kitid == 33){//efect 3 Miner //OK
$player->addEffect(Effect::getEffect(3)->setDuration(10000*20)->setAmplifier(4)->setVisible(true));	
}
		$player->sendMessage(TextFormat::RED ."[WallPVP]KITを再装着しました。");
	}
///////////////////////////////////////////
public function oanmotion(PlayerMoveEvent $event){
		$playera = $event->getPlayer();
	$name = $playera->getName();
			$ds = 16;
	if(isset($this->team[1][$name])){
		$x=75;
		$y=82;
		$z=23;
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
	$playera->addEffect(Effect::getEffect(4)->setDuration(5*20)->setAmplifier(0)->setVisible(true));
			}}
			
	if(isset($this->team[2][$name])){
		$x=70;
		$y=78;
		$z=138;
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
	$playera->addEffect(Effect::getEffect(4)->setDuration(5*20)->setAmplifier(0)->setVisible(true));
			}}

    }
	


////
public function onmotion(PlayerInteractEvent $event){
		$player = $event->getPlayer();
	$name = $player->getName();
	$hitem =$player->getInventory()->getItemInHand(); 
	if($hitem->getID()== 351 and $hitem->getDamage() == 5 and isset($this->joinedpvp[$name]) ){
		$directionVector = $player->getDirectionVector()->multiply(3);
		$player->setMotion($directionVector);
	
}elseif($hitem->getID()== 351 and $hitem->getDamage() == 6){
		$directionVector = $player->getDirectionVector()->multiply(5);
		$player->setMotion($directionVector);
		/////////////////

		/////////////////
}elseif($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR  and isset($this->joinedpvp[$name])){
	if($hitem->getID()== 351 and $hitem->getDamage() == 1){
		$directionVector = $player->getDirectionVector()->multiply(3);
		$player->setMotion($directionVector);
}elseif($hitem->getID()== 351 and $hitem->getDamage() == 2){
		$directionVector = $player->getDirectionVector()->multiply(5);
		$player->setMotion($directionVector);
}

	}
    }
	
public function onInteract(PlayerInteractEvent $event){
	
$player = $event->getPlayer();
$players = Server::getInstance()->getOnlinePlayers();
$hitem =$player->getInventory()->getItemInHand();
$name = $event->getPlayer()->getName();
$block = $event->getBlock();
$level = $player->getLevel();
$blockid = $block->getID();
$x = $block->x;
$y = $block->y;
$z = $block->z;

if($player->getHealth() >6  and isset($this->joinedpvp[$name])){
if($hitem->getID()== 351 and $hitem->getDamage() == 7){
	
	foreach($players as $playera){
		$ds = 10;
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
	$playera->addEffect(Effect::getEffect(19)->setDuration(30*20)->setAmplifier(1)->setVisible(true));
			}
	}
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 8){
		$ds = 10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
		$playera->addEffect(Effect::getEffect(15)->setDuration(30*20)->setAmplifier(1)->setVisible(true));
		
	}}
	
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 9){
			$ds = 10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
$ev = new EntityDamageByEntityEvent($player, $playera, EntityDamageEvent::CAUSE_ENTITY_ATTACK ,$ap);
			$playera->attack($ap, $ev);
	}}		
			
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 10){
					
					$ds = 10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
$playera->setKnockBack(5);
	}}		
	
			
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 11){
							
			$ds = 10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
		$playera->addEffect(Effect::getEffect(9)->setDuration(30*20)->setAmplifier(4)->setVisible(true));
		
	}}
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 12){
												$ds = 10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
		$playera->addEffect(Effect::getEffect(4)->setDuration(30*20)->setAmplifier(0)->setVisible(true));
		
	}}
	
	}elseif($hitem->getID()== 351 and $hitem->getDamage() == 3){$ds =10;
			foreach($players as $playera){
	if(abs($playera->getX()-$x)<=$ds and abs($playera->getY()-$y)<=$ds and abs($playera->getZ()-$z)<=$ds){
		$playera->setOnFire();
		
	}
}

$ev = new EntityDamageByEntityEvent($player, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK , 6);
			$playera->attack(6, $ev);
			
			
			
	
	return true;
	}}
    }	





///////////////////////////////////end///////////////////////////////

}
