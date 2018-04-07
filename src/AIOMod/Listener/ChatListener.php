<?php
namespace AIOMod\Listener;
use pocketmine\{Server, Player};
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerChatEvent;
use AIOMod\Loader;
class ChatListener implements Listener{
	public function onChat(PlayerChatEvent $event){
	$player = $event->getPlayer();
	$name = $player->getName();
	$ucfg = new Config("/AIOMod/Spieler/".$name.".yml", Config::YAML);
	if($ucfg->get("Aktiv") != null){
		$rfile = new Config("/AIOMod/Bans/".$ucfg->get("Aktiv").".yml", Config::YAML);
		if($rfile->get("Typ") == "Mute"){
			$player->sendMessage( "§7[§c§lAIOMod§r§7] §cDir wurde der Zugang zum Chat gesperrt. \n§cGrund: §4".$rfile->get("Grund")."§7[§4".$ucfg->get("Aktiv")."§7] \n§cGemutet bis: §c".$rfile->get("Dauer")."");
			$event->setCancelled(true);
		}
	}
}
}
