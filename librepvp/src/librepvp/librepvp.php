<?php

namespace librepvp;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\level\Position;
use pocketmine\level\Explosion;
use pocketmine\tile\Tile;
use pocketmine\event\Event\getEventName;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\IPlayer;
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
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\scheduler\PluginTask;
use pocketmine\entity\Effect;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\entity\Snowball;
use pocketmine\event\entity\ProjectileLaunchEvent;

class librepvp extends PluginBase implements Listener{
		 public function onEnable(){
			 
	/*		
$salt ="ask";
$authcode = $salt.gmdate("Y/m/d H:i");
$authhash = hash( 'SHA256', $authcode ); 
$hashed0_code = hash( 'SHA256', $authhash.'pasdsdsdsfdss' ); 
$hashed_code = hash( 'SHA256', $hashed0_code.'skatdsdsdsaa' ); 
echo $hashed_code;
*/
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->team = [1 => [] , 2 => [] ];
		
	/*	if(!file_exists($this->getDataFolder() . "players.sqlite3")){
			$this->database = new \SQLite3($this->getDataFolder() . "players.sqlite3", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

			$this->database->exec('CREATE TABLE players (
  name TEXT PRIMARY KEY,
  ip TEXT,
  cid TEXT,
  kill TEXT,
  renkill TEXT,
  isop TEXT,
  exp TEXT
);');
		}else{
			$this->database = new \SQLite3($this->getDataFolder() . "players.sqlite3", SQLITE3_OPEN_READWRITE);
		}
	*/	
#		$cmd ="stop";
#		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $cmd);
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);
		}
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array(
			"↓libreの設定ファイル"=> null,
			"鯖自体のID" => 5,
			"鯖自体のパスワード" => 5,
			"3" => 5,
			"4" => 5,
			));
		}else{
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array());
		}

		if(!file_exists($this->getDataFolder() . "player.yml")){
			$this->pset = new config($this->getDataFolder() . "player.yml", config::YAML, array(
			"↓libreのプレイヤーファイル"=> null,
			"例" => 5,
			 "player"=> array(
        		"ip"=> null,
				"cid"=> "aaa",
        		"kill"=> 5,
      			"renkill"=> 48046,
				"isop_" => 5,
				"exp" => 5,
				"例おしまい"=>0000000,
			)));
		}else{
			$this->pset = new config($this->getDataFolder() . "player.yml", config::YAML, array());
		}

		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		
        	if(is_dir($this->getServer()->getPluginPath()."EconomyAPI")){
				$this->getLogger()->info(TextFormat::GREEN."Economy読み込んだよ!");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."EconomyAPIなかったよ～");
				$this->economy = false;
			}
			Server::getInstance()->loadLevel("pvp");
			Server::getInstance()->loadLevel("seika2");
			}
			
	 public function onDisable(){
        unset($this->players);
		unset($this->joined);



		foreach($this->getServer()->getOnlinePlayers() as $p){
			$name = $p->getName();
					unset($this->team[1][$name]);
		unset($this->team[2][$name]);
				global ${"joined_".$name} ;
				${"joined_".$name} = 0;
				global ${"joinedli_".$name};
				global ${"joined_".$name};
				${"joined_".$name} = 0;
				${"joinedli_".$name} = 0;
				global ${"joinedpvp_".$name};
				${"joinedpvp_".$name} = 0;
				$level = Server::getInstance()->getLevelByName("world");
				$pos = new Position(128,71,128,$level);
				$p->teleport($pos);
				$p->kick("鯖がリロードまたは終了しました。");
		}
        $this->getLogger()->info(TextFormat::RED ."終了しました。");
    }
	
	
/////こまんどせんだーー
public function onCommand(CommandSender $sender, Command $command,$label,array $args){
	switch($command->getName()){
	case "cid":{
                if(!isset($args[0])){
					$sender->sendMessage("Usege: /cid プレイヤー名");
					break;
								}


				foreach($this->getServer()->getOnlinePlayers() as $p){
				$k2name = $p->getName();
				if($k2name == $args[0]){
					$okkick = 1;
				}
								}
				if(!isset($okkick)){
				$sender->sendMessage(TextFormat::RED. " [LibrePVP] そんな人はいません。");
				break;
				}

									
				$playerk = $this->getServer()->getPlayer($args[0]);
				$name = $sender->getName();
				$kname =  $playerk->getName();
				$cfg = $this->pset->getAll();
				$cid = $playerk->loginData["clientId"];
				$fcid = $cfg[$kname]["cid"];
				$sender->sendMessage(TextFormat::RED. "[LibrePVP]".$kname."のcidは".$cid."です。初回ログイン時のcidは".$fcid."です。");

				break;
				
				
	}
	case "mi":{
		$this->pset->reload() ;
		$name = $sender->getName();	
		$cfg = $this->pset->getAll();
		$killcount = $cfg[$name]["kill"];
		$exp = $cfg[$name]["exp"];
		$renkill = $cfg[$name]["renkill"];
				if($this->team[1][$name] == 1){
		$team ="レッド";
		}else{
		$team ="ラピ";
		}
		$sender->sendMessage(TextFormat::RED ."////////////////////////////////////////");
		$sender->sendMessage(TextFormat::RED ."所属チーム = ".$team."チーム");
		$sender->sendMessage(TextFormat::RED ."キル数 = ".$killcount."kill");
		$sender->sendMessage(TextFormat::RED ."連続キル数 = ".$renkill."kill");
		$sender->sendMessage(TextFormat::RED ."EXP = ".$exp."EXP");
		$sender->sendMessage(TextFormat::RED ."////////////////////////////////////////");
		break;
	}
	case "seika2":{
$time = time() + 9*3600;  //GMTとの時差9時間を足す
$nowj = gmdate("H", $time);
global ${"joinedpvp_".$name};
$name = $sender->getName();	
		if($nowj <6 and $nowj >1 ){
			
		$sender->sendMessage(TextFormat::RED ."[LibrePVP]生活エリアの営業は6時から24時です。");	
		break;
		}elseif(${"joinedpvp_".$name} != 1){
			$name = $sender->getName();	
		$sender->teleport(Server::getInstance()->getLevelByName("world")->getSafeSpawn());
		$vector = new vector3(128,71,128);//座標を指定
		$sender->teleport($vector);
		$sender->sendMessage(TextFormat::RED ."[LibrePVP]生活エリアに移動しました。");
		global ${"joinedli_".$name} ;
		${"joinedli_".$name} = "in";
		break;
	}
	}
		case "leave":{
		$name = $sender->getName();	
		global ${"joinedpvp_".$name};
		global ${"joinedli_".$name} ;
		if(${"joinedli_".$name} === "in" and ${"joinedpvp_".$name} != 1){
		$sender->teleport(Server::getInstance()->getLevelByName("world")->getSafeSpawn());
		$vector = new vector3(128,71,128);//座標を指定
		$sender->teleport($vector);
		$sender->sendMessage(TextFormat::RED ."[LibrePVP]生活エリアから離れました。");
		${"joinedli_".$name} = 0;
	}
	 	break;
		}
		
		case "hjoin":{
		$name = $sender->getName();
		global ${"teamdaia_".$name} ;
		global ${"joinedpvp_".$name};
		
			if(${"joinedpvp_".$name} >= 1){
				$sender->sendMessage(TextFormat::RED ."[SekaiPVP]あなたはすでに拠点に移動しました。");
			}else{
				if($this->team[1][$name] === 1){
					$sender->sendMessage(TextFormat::RED. " [SekaiPVP]ダイヤチームの拠点に移動します。");
					$sender->teleport(Server::getInstance()->getLevelByName("pvp")->getSafeSpawn());
					$vector = new vector3(122,96,20);//座標を指定
					$sender->teleport($vector);
					global ${"joinedpvp_".$name};
					${"joinedpvp_".$name} = 1;
				}else{
					$sender->sendMessage(TextFormat::RED ."[SekaiPVP]エメラルドチームの拠点に移動します。");
					$level = Server::getInstance()->getLevelByName("pvp");
					$sender->teleport(Server::getInstance()->getLevelByName("pvp")->getSafeSpawn());
					$vector = new vector3(122,96,20);//座標を指定
					$sender->teleport($vector);
					global ${"joinedpvp_".$name};
					${"joinedpvp_".$name} = 1;
										}
	$effect = Effect::getEffect(11); //Effect ID
	$effect->setVisible(true); //Particles
	$effect->setAmplifier(1000);
	$effect->setDuration(100); //Ticks
	$sender->addEffect($effect);	
									break;
				}
			}
	
	}
}
///////


/////死んだときイベント////
 public function onPlayerDeath(PlayerDeathEvent $event){
        $ev = $event->getEntity()->getLastDamageCause();
        if ($ev instanceof EntityDamageByEntityEvent) {
            $killer = $ev->getDamager();
            if ($killer instanceof Player) {
                $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($killer->getName(), 50);
                $killer->sendMessage(""."[LibrePVP]"." 50$"."得ました。"."");
				$name = $event->getEntity()->getName();		
				
				if($this->team[1][$name] == 1){
				while($this->corehp[1][$mt_ran] >0){
				$mt_ran =mt_rand(1,3);
				}
				$this->corehp[1][$mt_ran] = $this->corehp[1][$mt_ran] + 1;
				}else{
				while($this->corehp[2][$mt_ran] >0){
				$mt_ran =mt_rand(1,3);
				}
				$this->corehp[2][$mt_ran] = $this->corehp[2][$mt_ran] + 1;
				}
				
				$killername =  $killer->getName();
				$cfg = $this->pset->getAll();
				$killerbf = $cfg[$killername]["kill"];
				$killernow = $killerbf + 1;
				$killrenbf = $cfg[$killername]["renkill"];
				$killrennow = $killrenbf + 1;
				$this->pset->set(array("player"=>array("kill"=>$killernow)));//値と名前を設定
				$this->pset->save();//設定を保存
				$this->pset->set(array("player"=>array("renkill"=>$killrennow)));//値と名前を設定
				$this->pset->save();//設定を保存
				$this->pset->reload;
			
				$this->coff($killer);
				$this->con($killer);
			foreach($event->getEntity()->getEffects() as $effect){
				$event->getEntity()->removeEffect($effect->getId());
			}
				
					

            }
        
			$player = $event->getEntity();
			global ${"joined_".$name} ;
			${"joined_".$name} = 0;
								global ${"joinedpvp_".$name};
					${"joinedpvp_".$name} = 0;
				global	${"joinedli_".$name};
					${"joinedli_".$name} = 0;
			$pos = new Position(128,71,128,world);//座標を指定
			$player->teleport($pos);
		}
		}

	
////join字
	public function joinplayer(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$cid = $player->loginData["clientId"];
		$ip = $player->getAddress();
/*		
		$prepare = $this->database->prepare("SELECT * FROM players WHERE name = :name");
		$prepare->bindValue(":name", $name, SQLITE3_TEXT);
		$result = $prepare->execute();
		if($result instanceof \SQLite3Result){
			$data = $result->fetchArray(SQLITE3_ASSOC);
			$result->finalize();
			if(isset($data["name"]) and $data["name"] === $name){
		$prepare = $this->database->prepare("INSERT INTO players (name, ip, cid, kill, renkill, isop, exp) VALUES (:name, :ip, :cid, :kill, :renkill, :isop, :exp)");
		$prepare->bindValue(":name", $name, SQLITE3_TEXT);
		$prepare->bindValue(":ip", $ip, SQLITE3_TEXT);
		$prepare->bindValue(":cid", $cid, SQLITE3_TEXT);
		$prepare->bindValue(":kill", "0", SQLITE3_TEXT);
		$prepare->bindValue(":renkill", "0", SQLITE3_TEXT);
		$prepare->bindValue(":isop", "0", SQLITE3_TEXT);
		$prepare->bindValue(":exp", "0", SQLITE3_TEXT);
		$prepare->execute();
*/
#	$uuid = $player->getUniqueId();
if($this->pset->exists($name)){
		$cfg = $this->pset->getAll();
		$killcount = $cfg[$name]["kill"];
		$exp = $cfg[$name]["exp"];	
	}else{
		$this->pset = new config($this->getDataFolder() . "player.yml", config::YAML, array(
		$name=> array(
        			"ip"=> $ip,
					"cid"=> $cid,
#					"uuid"=> $uuid,
        			"kill"=> 0,
      				"renkill"=> 0,
					"isop" => 0,
					"exp" => 0,
					)));
		$this->pset->save();//設定を保存
		$killcount = 0;
		$exp = 0;
		$this->pset->reload() ;
		}
////
	$killcount = 0;
		$exp = 0;
	if(count($this->team[1]) <= count($this->team[2])){
		 	
			
			if(!$event->getPlayer()->isOp() and count($this->team[1]) <= count($this->team[2])){
				$player->setNameTag(TextFormat::AQUA . "[レッド]"."[".$killcount."kill,".$exp."EXP]".$player->getDisplayName()."");
				$this->team[1][$name] = 1;
				$player->sendMessage(TextFormat::RED ."[LibrePVP]あなたはレッドチームです。");
			}elseif(count($this->team[1]) <= count($this->team[2])){
				
				$player->setNameTag(TextFormat::AQUA . "[レッド]"."[OP][".$killcount."kill,".$exp."EXP]".$player->getDisplayName()."");
				$this->team[1][$name] = 1;
				$player->sendMessage(TextFormat::RED ."[LibrePVP]あなたはレッドチームです。");
			}elseif(!$event->getPlayer()->isOp()){
				$this->team[2][$name] = 1;
				$player->sendMessage(TextFormat::RED ."[LibrePVP]あなたはラピチームです。");
				$player->setNameTag(TextFormat::GREEN . "[ラピ]"."[".$killcount."kill,".$exp."EXP]".$player->getDisplayName()."");
			}elseif($event->getPlayer()->isOp()){
				$this->team[2][$name] = 1;
				$player->sendMessage(TextFormat::RED ."[LibrePVP]あなたはラピチームです。");
				$player->setNameTag(TextFormat::GREEN . "[ラピ]"."[OP][".$killcount."kill,".$exp."EXP]".$player->getDisplayName()."");
			}
		}
/////
	
	$this->coff($player);
	$this->con($player);

	
	
	}

///プレイヤー退出時
	public function onPlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$player->setNameTag("");
		$name = $player->getName();
		global ${"joinedli_".$name};
		global ${"joined_".$name};
		unset($this->team[1][$name]);
		unset($this->team[2][$name]);
		${"joined_".$name} = 0;
		${"joinedli_".$name} = 0;
		global ${"joinedpvp_".$name};
		${"joinedpvp_".$name} = 0;
		unset($this->joined[$name]);
	}
/////
	
////////////////////////ダメー時・・・

	public function onHurt(EntityDamageEvent $pf){
		if($pf instanceof EntityDamageByEntityEvent) {
			if($pf->getDamager() instanceof Player && $pf->getEntity() instanceof Player) {
				$sender = $pf->getDamager();
				$reciever = $pf->getEntity();
				unset($sname);
				unset($rname);
				$sname = $sender->getName();
				$rname = $reciever->getName();
				global ${"joinedpvp_".$sname};
				if(${"joinedpvp_".$sname} !=1){
					$pf->setCancelled();
				}
					if($this->team[1][$sname] == 1 and  $this->team[1][$rname] == 1){
			                        $pf->setCancelled();
                    }elseif($this->team[2][$sname] == 1 and   $this->team[2][$rname] == 1){
                        			$pf->setCancelled();
          			}else{
                     				return true;
                    			}
				}
			}
	}
	////////

	///////試合終了時
	public function theendtime(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
		global ${"joinedpvp_".$name};
		if(${"joinedpvp_".$name} === 1){
		$pos = new Position(128,71,128,world);//座標を指定
		$player->teleport($pos);
		${"joinedpvp_".$name} = 0;
		}
	}
	}
	//////
	
/////醍醐味　コアについて
	public function onEntityClose(EntityDespawnEvent $event){
		if($event->getType() === 81){	//81=Snowball
			$entity = $event->getEntity();
			$ballid = $entity->getId();
			$shooter = $entity->shootingEntity;
			$posTo = $entity->getPosition();
			$posX = floor($entity->getPosition()->getX());
			$posY = floor($entity->getPosition()->getY());
			$posZ = floor($entity->getPosition()->getZ());
			$posXm = $posX -1;
			$posXp = $posX +1;
			$posYm = $posY -1;
			$posYp = $posY +1;
			$posZm = $posZ -1;
			$posZp = $posZ +1;
			if($posTo instanceof Position){
				if($shooter instanceof Player  && !$event->isHuman()){
						$level = $entity->getLevel();
					//////座標の決定
					////y//
			$vector = new Vector3($posX,$posYm,$posZ);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posX,$posYm,$posZ);
			}
			////
			////
			$vector = new Vector3($posX,$posYp,$posZ);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posX,$posYp,$posZ);
			}
			////y////
			
			////x//
			$vector = new Vector3($posXp,$posY,$posZ);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posXp,$posY,$posZ);
			}
			////
			////
			$vector = new Vector3($posXm,$posY,$posZ);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posXm,$posY,$posZ);
			}
			////x//
			
			////z/
			$vector = new Vector3($posX,$posY,$posZp);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posX,$posY,$posZp);
			}
			////
			////
			$vector = new Vector3($posX,$posY,$posZm);
			$block = $level->getBlock($vector); //Blockオブジェクトの取得
			if($block->getID() !=0){
			$kvector  = new Vector3($posX,$posY,$posZm);
			}
			////z/
			/////////
			if($name = $shooter->getName()){
				if($this->team[1][$name] == 1){
						$block = Block::get(1,0);//Blockオブジェクトの生成
						if(isset($kvector)){
						$level->setBlock($kvector, $block);
						}
				}
						
			}

						
					}
				}
			}
		
	}
//////

/////看板

//////

	
///////chat関連
	public function con(Player $player){
        	unset($this->players[$player->getName()]);
    	}
	
	public function coff(Player $player){
	        $this->players[$player->getName()] = $player->getName();
	}
	public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
		$name = $player->getName();
		$cfg = $this->pset->getAll();
		$killcount = $cfg[$name]["kill"];
		$exp = $cfg[$name]["exp"];
        if($player instanceof Player){
            if(!$event->isCancelled()){
                $user = $player->getName();
                $message = $event->getMessage();
                
			if($this->team[1][$name] == 1){
       			if(!$event->getPlayer()->isOp()){
				$format = "".TextFormat::AQUA . "[レッド]"."[".$killcount."kill,".$exp."EXP]<".$user.">".TextFormat::RESET.$message;
				}else{
				$format = "".TextFormat::AQUA . "[レッド]"."[OP][".$killcount."kill,".$exp."EXP]<".$user.">".TextFormat::RESET.$message;	
				}
			}else{
				 if(!$event->getPlayer()->isOp()){
				$format = "".TextFormat::GREEN . "[ラピ]"."[".$killcount."kill,".$exp."EXP]<".$user.">".TextFormat::RESET.$message;
				}else{
				$format = "".TextFormat::GREEN . "[ラピ]"."[OP][".$killcount."kill,".$exp."EXP]<".$user.">".TextFormat::RESET.$message;	
				}
				}
				
				
				$this->getLogger()->info($format);
				
                foreach($this->getServer()->getOnlinePlayers() as $p){
                        $p->sendMessage($format);
                    }
                $event->setCancelled();
		}
        }unset($killcount);
		unset($exp);
    }
	


}
////////chat関連おしまい
