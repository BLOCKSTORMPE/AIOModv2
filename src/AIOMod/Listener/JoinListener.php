<?php
namespace AIOMod\Listener;
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\Config;
use DateTime;
class JoinListener implements Listener{
	public function onJoin(PlayerPreLoginEvent $event){

	$player = $event->getPlayer();
  	 $name = $player->getName();
  	 $ip = $player->getAddress();
if($player->hasPermission("aiomod.notify")){
   if(!file_exists("/AIOMod/Notify/" . $name . ".yml")){
       $cfg = new Config("/AIOMod/Notify/" . $name . ".yml", Config::YAML);
$cfg->set("Notify", true);
$cfg->save();
}
}
  	 if(file_exists("/AIOMod/Spieler/".$name.".yml")){
  	 	 	$banned = new Config("/AIOMod/Spieler/".$name.".yml", Config::YAML);
  	 	 if($banned->get("Aktiv") != null){
  				$id = $banned->get("Aktiv");
  				$cfg = new Config("/AIOMod/Bans/" . $id . ".yml", Config::YAML);
if($cfg->get("Typ") == "Ban"){
  	 	 	 if($cfg->get("Dauer") == "Permanent"){
  	 	 	 	 $player->kick("§cDein Account auf dem §bNetzwerk§c wurde gesperrt. \n §cGrund: §e" . $banned->get("Grund") . "§7[§c" . $cfg->get("Aktiv") . "§7] §7| §cDauer: §4PERMANENT \n§aEA: §b§lrevengermc.de/support", false);
  	 	 	 }else{
  	 	 	 	if(new DateTime("now") < new DateTime($cfg->get("Dauer"))){


  	 	 	 			$player->kick("§cDein Account auf dem §bNetzwerk §cwurde gesperrt§r\n§eGrund: §c".$cfg->get("Grund")."§7[§4" . $banned->get("Aktiv") . "§7] | §eGebannt bis:§4 " . $cfg->get("Dauer") . "  \n §aEA revengermc.de/support/", false);

  	 	 	 	}else{

  	 	 	 		$banned->set("Aktiv", null);
       $banned->save();
       $cfg->set("Status", "Inaktiv");
       $cfg->save();
  	 	 	 	}
  	 	 	 }
}
  	 	 	 }

  	 }else{
  		$banned = new Config("/AIOMod/Spieler/".$name.".yml", Config::YAML);
   $banned->set("Aktiv", null);
   $banned->set("Punkte", null);
   $banned->save();
  	 }

  }
}
