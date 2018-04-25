<?php
namespace AIOMod;
#Plugin-Imports
use AIOMod\Tasks\Check;
use AIOMod\Listener\ChatListener;
use AIOMod\Listener\JoinListener;
use AIOMod\Command\NotifyCommand;
use pocketmine\scheduler\PluginTask;

use pocketmine\{Server, Player};
use pocketmine\utils\{Config, Scheduler};
use pocketmine\plugin\PluginTask;
use pocketmine\command\{Command, CommandSender, CommandExecutor};
use pocketmine\event\Listener;

use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener{

	public $prefix = "§7[§c§lAIOMod§r§7]";
	public function onEnable(){
		#Autocreating Folders needed by the system
@mkdir("/AIOMod/");
@mkdir("/AIOMod/Bans/");
@mkdir("/AIOMod/Spieler/");
@mkdir("/AIOMod/Notify/");
		$oldreports = scandir("/AIOMod/Bans/");
		$this->getServer()->getPluginManager()->registerEvents(new JoinListener($this), $this);
$this->getServer()->getPluginManager()->registerEvents(new ChatListener($this), $this);
	$this->getServer()->getScheduler()->scheduleRepeatingTask(new Check($this, $oldreports), 20);
		}
function randomString($length = 5) {
    $str = "";
    $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
 }
		public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
if(strtolower($cmd) == "notify"){
				if($sender instanceof Player){
					if($sender->hasPermission("aiomod.notify")){
						$tcfg = new Config("/AIOMod/Notify/".$sender->getName().".yml", Config::YAML);
						if($tcfg->get("Notify") == true){
							$tcfg->set("Notify", false);
$tcfg->save();
							$sender->sendMessage($this->prefix ." Benachrichtigungen wurden deaktiviert");

						}
if($tcfg->get("Notify") == false){
							$tcfg->set("Notify", true);
							$tcfg->save();
							$sender->sendMessage($this->prefix ."Benachrichtigungen wurden aktiviert");
						}
					}
				}
			}
			if(strtolower($cmd) == "locklist"){
				if($sender->hasPermission("aiomod.locklist")){
					$files = scandir("/AIOMod/Bans/");

						foreach($files as $report){
							$filename = str_replace(".yml", "", $report);
								if($filename != "." && $filename != ".."){

									$configr = new Config("/AIOMod/Bans/".$filename.".yml", Config::YAML);
									$configu = new Config("/AIOMod/Spieler/".$configr->get("Name").".yml", Config::YAML);
if($configr->get("Status") == "Aktiv"){
									$grund = $configr->get("Grund");
									$spieler = $configr->get("Name");
									$typ = $configr->get("Typ");
									$id = $configu->get("Aktiv");



									$sender->sendMessage($this->prefix ."§b§l<§r §c".$spieler." §7| §c".$grund." §7| §c".$id." §7| §c".$typ."§b§l>");
                }
								}
							}
						}else{
							$sender->sendMessage("§cDu hast keine Rechte um diesen Befehl auszuführen");
						}
					}

			if(strtolower($cmd) == "unlock"){
				if($sender->hasPermission("aiomod.unlock")){
					if(isset($args[0])){
						if(file_exists("/AIOMod/Spieler/". $args[0] . ".yml")){
							$cfg = new Config("/AIOMod/Spieler/". $args[0].".yml", Config::YAML);
							if($cfg->get("Aktiv") != null){
								$bcfg = new Config("/AIOMod/Bans/".$cfg->get("Aktiv").".yml", Config::YAML);
								$bcfg->set("Status", "Inaktiv");
								$bcfg->save();
								$cfg->set("Aktiv", null);
								$cfg->save();
								$sender->sendMessage($this->prefix."§aSpieler wurde erfolgreich entsperrt");
							}else{
								$sender->sendMessage($this->prefix."§cDieser Spieler hat keine aktive Bestrafung");
							}
						}else{
							$sender->sendMessage($this->prefix."§cDieser Spieler hat das Netzwerk noch nie betreten");
						}
					}else{
						$sender->sendMessage($this->prefix."§cFalsche Syntax. Benutze §6/unlock <Spieler>");
					}
				}else{
					$sender->sendMessage($this->prefix."§cDu hast keine Rechte um diesen Befehl auszuführen");
				}
			}
			if(strtolower($cmd) == "playerinfo"){
				if($sender->hasPermission("aiomod.playerinfo")){
					if(isset($args[0])){
						if(file_exists("/AIOMod/Spieler/" . $args[0] . ".yml")){
							$cfg = new Config("/AIOMod/Spieler/" . $args[0] . ".yml", Config::YAML);
							$sender->sendMessage($this->prefix."§bSpielerinformationen für §6". $args[0] . "§b:");
							$sender->sendMessage($this->prefix."§bPunkte: §c". $cfg->get("Punkte")."");
							if($cfg->get("Aktiv") == null){
								$status = "Nicht bestraft";
							}else{
								$status = "Bestraft";
							}
							$sender->sendMessage($this->prefix."§bAktuell Bestraft: §6". $status."");
							if($status != null){
								$sender->sendMessage($this->prefix."§bBestrafungs-ID: §". $cfg->get("Aktiv")."");
							}
						}else{
							$sender->sendMessage($this->prefix."§cDieser Spieler ist nicht im Bestrafungssystem registriert");
					}
				}else{
					$sender->sendMessage($this->prefix."§cFalsche Syntax. Benutze §6/playerinfo <Spieler>");
				}
			}else{
				$sender->sendMessage($this->prefix."§cDu hast keine Rechte um diesen Befehl auszuführen");
			}
		}


			if(strtolower($cmd) == "baninfo"){
				if($sender->hasPermission("aiomod.baninfo")){
					if(isset($args[0])){
						if(file_exists("/AIOMod/Bans/" . $args[0] . ".yml")){
							$config = new Config("/AIOMod/Bans/" . $args[0] . ".yml", Config::YAML);
							$sender->sendMessage($this->prefix."§bBaninformationen für §6". $args[0] . ":");
							$sender->sendMessage($this->prefix."§bSpieler: §6". $config->get("Name")."");
							$sender->sendMessage($this->prefix."§bBestraft von: §6" . $config->get("Moderator")."");
							$sender->sendMessage($this->prefix."§bGrund: §6" . $config->get("Grund")."");
							$sender->sendMessage($this->prefix."§bTyp: §6" . $config->get("Typ")."");
							$sender->sendMessage($this->prefix."§bBestraft bis: §6" . $config->get("Dauer")."");
							$sender->sendMessage($this->prefix."§bNotiz des Moderators: §6". $config->get("Notiz")."");
						}else{
							$sender->sendMessage($this->prefix."§cDiese Bestrafung ist nicht im System registriert. Bitte achte auf Groß und Kleinschreibung");
						}
					}else{
						$sender->sendMessage($this->prefix . "§cFalsche Syntax. Richtig: §6/baninfo <BanID>");
					}
				}else{
					$sender->sendMessage($this->prefix."§cDu hast keine Rechte um diesen Befehl auszuführen");
				}
			}

			if(strtolower($cmd) == "lock"){

					if($sender->hasPermission("aiomod.lock")){
						if(isset($args[0]) || isset($args[1])){
							if(file_exists("/AIOMod/Spieler/" . $args[0] . ".yml")){
								$userconf = new Config("/AIOMod/Spieler/". $args[0] . ".yml", Config::YAML);
								if($userconf->get("Aktiv") == null){
if(strtolower($args[1]) == "hacking" || strtolower($args[1]) == "hacks" || strtolower($args[1]) == "teaming" || strtolower($args[1]) == "team" || strtolower($args[1]) == "bugusing" || strtolower($args[1]) == "bug" || strtolower($args[1]) == "chatverhalten" || strtolower($args[1]) == "chat" || strtolower($args[1]) == "spamming" || strtolower($args[1]) == "spam" || strtolower($args[1]) == "werbung" || strtolower($args[1]) == "wer" || strtolower($args[1]) == "extrem" || strtolower($args[1]) == "ext"){
								$id = $this->randomString();
								$config = new Config("/AIOMod/Bans/" . $id . ".yml", Config::YAML);
								$uconf = new Config("/AIOMod/Spieler/" . $args[0] . ".yml", Config::YAML);
								$points = $uconf->get("Punkte");
								$config->set("Name", $args[0]);
								$config->set("Status", "Aktiv");

								$config->set("Moderator", $sender->getName());
								if(isset($args[2])){
									$config->set("Notiz", $args[2]);
								}else{
									$config->set("Notiz", null);
								}
								$eind = new \DateTime("now");
								$eind->add(new \DateInterval("P1D"));
								$dreid = new \DateTime("now");
								$dreid->add(new \DateInterval("P1D"));
								$siebend = new \DateTime("now");
								$siebend->add(new \DateInterval("P1D"));
								$dreizd = new \DateTime("now");
								$dreizd->add(new \DateInterval("P1D"));
								$neunzd = new \DateTime("now");
								$neunzd->add(new \DateInterval("P1D"));
								if(strtolower($args[1]) == "hacking" || strtolower($args[1]) == "hacks"){
									$config->set("Grund", "Hacking/Clientmods");
									if($points == 0){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $neunzd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Ban");
								}

								if(strtolower($args[1]) == "teaming" || strtolower($args[1]) == "team"){
									$config->set("Grund", "Teaming");
									if($points == 0){
										$config->set("Dauer", $dreid->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Ban");
								}
								if(strtolower($args[1]) == "bugusing" || strtolower($args[1]) == "bug"){
									$config->set("Grund", "Bugusing");
									if($points == 0){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $neunzd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Ban");
								}
								if(strtolower($args[1]) == "chatverhalten" || strtolower($args[1]) == "chat"){
									$config->set("Grund", "Chatverhalten");
									if($points == 0){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $neunzd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Mute");
								}
								if(strtolower($args[1]) == "spamming" || strtolower($args[1]) == "spam"){
									$config->set("Grund", "Spamming");
									if($points == 0){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $neunzd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Mute");
								}
								if(strtolower($args[1]) == "werbung" || strtolower($args[1]) == "wer"){
									$config->set("Grund", "Werbung");
									if($points == 0){
										$config->set("Dauer", $siebend->format('Y-m-d H:i:s'));
									}
									if($points == 1){
										$config->set("Dauer", $dreizd->format('Y-m-d H:i:s'));
									}
									if($points == 2){
										$config->set("Dauer", $neunzd->format('Y-m-d H:i:s'));
									}
									if($points > 3 || $points == 3){
									$config->set("Dauer", "Permanent");
									}
									$config->set("Typ", "Mute");
								}
								if(strtolower($args[1]) == "extrem" || strtolower($args[1]) == "ex"){
									$config->set("Grund", "Extrem");


									$config->set("Dauer", "Permanent");

									$config->set("Typ", "Ban");
								}
							$uconf->set("Punkte", $uconf->get("Punkte")+1);
							$uconf->set("Aktiv", $id);
							$uconf->save();
							$config->save();
$sender->sendMessage($this->prefix . "§a Bestrafung wurde erfolgreich mit der ID §5" . $id
 . " §aerstellt!");
}else{
$sender->sendMessage($this->prefix . " §cDas ist kein gültiger Grund, einen Spieler zu bestrafen!");
}
}else{
	$sender->sendMessage($this->prefix . "§cDieser Spieler hat bereits eine Strafe");
	}
						}else{
							$sender->sendMessage($this->prefix . " §cDieser Spieler hat das Netzwerk noch nie betreten!");
					}
				}else{
					$sender->sendMessage($this->prefix . " §cFalsche Syntax. Benutze /lock <Spieler> <Grund> [Notiz]");
				}
			}else{
				$sender->sendMessage($this->prefix . " §cDu hast keine Rechte um diesen Befehl auszuführen");
		}
	}
return true;
}
}
 
