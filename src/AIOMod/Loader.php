<?php
namespace AIOMod;
use pocketmine\scheduler\PluginTask;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\{Server, Player};
use pocketmine\utils\{Config, Scheduler};
use pocketmine\command\{Command, CommandSender, CommandExecutor};
use pocketmine\event\Listener;
use DateTime;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\{PlayerChatEvent, PlayerPreLoginEvent};
class Loader extends PluginBase implements Listener{
	public $prefix = "§7[§c§lAIOMod§r§7]";
	public function onEnable(){
		$oldreports = scandir("/UniverseMC/AIOMod/Bans");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	$this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $oldreports), 20);
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
			if(strtolower($cmd) == "locklist"){
				if($sender->hasPermission("aiomod.lock")){
					$files = scandir("/UniverseMC/AIOMod/Bans/");

						foreach($files as $report){
							$filename = str_replace(".yml", "", $report);
								if($filename != "." && $filename != ".."){

									$configr = new Config("/UniverseMC/AIOMod/Bans/".$filename.".yml", Config::YAML);
									$configu = new Config("/UniverseMC/AIOMod/Spieler/".$configr->get("Name").".yml", Config::YAML);
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
						if(file_exists("/UniverseMC/AIOMod/Spieler/". $args[0] . ".yml")){
							$cfg = new Config("/UniverseMC/AIOMod/Spieler/". $args[0].".yml", Config::YAML);
							if($cfg->get("Aktiv") != null){
								$bcfg = new Config("/UniverseMC/AIOMod/Bans/".$cfg->get("Aktiv").".yml", Config::YAML);
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
				if($sender->hasPermission("aiomod.lock")){
					if(isset($args[0])){
						if(file_exists("/UniverseMC/AIOMod/Spieler/" . $args[0] . ".yml")){
							$cfg = new Config("/UniverseMC/AIOMod/Spieler/" . $args[0] . ".yml", Config::YAML);
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
				if($sender->hasPermission("aiomod.lock")){
					if(isset($args[0])){
						if(file_exists("/UniverseMC/AIOMod/Bans/" . $args[0] . ".yml")){
							$config = new Config("/UniverseMC/AIOMod/Bans/" . $args[0] . ".yml", Config::YAML);
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
							if(file_exists("/UniverseMC/Jump/" . $args[0] . ".yml")){
								$userconf = new Config("/UniverseMC/AIOMod/Spieler". $args[0] . ".yml", Config::YAML);
								if($userconf->get("Aktiv") == null){
if(strtolower($args[1]) == "hacking" || strtolower($args[1]) == "hacks" || strtolower($args[1]) == "teaming" || strtolower($args[1]) == "team" || strtolower($args[1]) == "bugusing" || strtolower($args[1]) == "bug" || strtolower($args[1]) == "chatverhalten" || strtolower($args[1]) == "chat" || strtolower($args[1]) == "spamming" || strtolower($args[1]) == "spam" || strtolower($args[1]) == "werbung" || strtolower($args[1]) == "wer" || strtolower($args[1]) == "extrem" || strtolower($args[1]) == "ext"){
								$id = $this->randomString();
								$config = new Config("/UniverseMC/AIOMod/Bans/" . $id . ".yml", Config::YAML);
								$uconf = new Config("/UniverseMC/AIOMod/Spieler/" . $args[0] . ".yml", Config::YAML);
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
public function onChat(PlayerChatEvent $event){
	$player = $event->getPlayer();
	$name = $player->getName();
	$ucfg = new Config("/UniverseMC/AIOMod/Spieler/".$name.".yml", Config::YAML);
	if($ucfg->get("Aktiv") != null){
		$rfile = new Config("/UniverseMC/AIOMod/Bans/".$ucfg->get("Aktiv").".yml", Config::YAML);
		if($rfile->get("Typ") == "Mute"){
			$player->sendMessage($this->prefix . " §cDir wurde der Zugang zum Chat gesperrt. \n§cGrund: §4".$rfile->get("Grund")."§7[§4".$ucfg->get("Aktiv")."§7] \n§cGemutet bis: §c".$rfile->get("Dauer")."");
			$event->setCancelled(true);
		}
	}
}

public function onJoin(PlayerPreLoginEvent $event){
	$player = $event->getPlayer();
  	 $name = $player->getName();
  	 $ip = $player->getAddress();

  	 if(file_exists("/UniverseMC/AIOMod/Spieler/".$name.".yml")){
  	 	 	$banned = new Config("/UniverseMC/AIOMod/Spieler/".$name.".yml", Config::YAML);
  	 	 if($banned->get("Aktiv") != null){
  				$id = $banned->get("Aktiv");
  				$cfg = new Config("/UniverseMC/AIOMod/Bans/" . $id . ".yml", Config::YAML);
  	 	 	 if($cfg->get("Dauer") == "Permanent"){
  	 	 	 	 $player->close("§cDein Account auf dem §bNetzwerk§c wurde gesperrt. \n §cGrund: §e" . $banned->get("Grund") . "§7[§c" . $cfg->get("Aktiv") . "§7] §7| §cDauer: §4PERMANENT \n§aEA: §b§lrevengermc.de/support");
  	 	 	 }else{
  	 	 	 	if(new DateTime("now") < new DateTime($cfg->get("Dauer"))){


  	 	 	 			$player->close("§cDein Account auf dem §bNetzwerk §cwurde gesperrt§r\n§eGrund: §c".$cfg->get("Grund")."§7[§4" . $banned->get("Aktiv") . "§7] | §eGebannt bis:§4 " . $cfg->get("Dauer") . "  \n §aEA revengermc.de/support/");

  	 	 	 	}else{

  	 	 	 		$banned->set("Aktiv", null);
       $banned->save();
       $cfg->set("Status", "Inaktiv");
       $cfg->save();
  	 	 	 	}
  	 	 	 }
  	 	 	 }

  	 }else{
  		$banned = new Config("/UniverseMC/AIOMod/Spieler/".$name.".yml", Config::YAML);
   $banned->set("Aktiv", null);
   $banned->set("Punkte", null);
   $banned->save();
  	 }
  }

 }
 class MessageTask extends PluginTask{

    public function __construct($plugin, $oldreports) {
        $this->plugin = $plugin;
        $this->oldreports = $oldreports;
        parent::__construct($plugin);
    }

    public function onRun($tick) {
			foreach(Server::getInstance()->getOnlinePlayers() as $p){
				$config = new Config("/UniverseMC/AIOMod/Spieler/". $p->getName() . ".yml", Config::YAML);
				if($config->get("Aktiv") != null){
					$cfg = new Config("/UniverseMC/AIOMod/Bans/". $config->get("Aktiv") . ".yml", Config::YAML);
						if($cfg->get("Typ") == "Ban"){
							if(new DateTime("now") < new DateTime($cfg->get("Dauer"))){
							$reason = $cfg->get("Grund");
							$id = $config->get("Aktiv");
							$dauer = $cfg->get("Dauer");
								$data = [
									'type' => 'form',
									'title' => "§cDu wurdest gebannt",
									'content' => "   §cDein Account wurde aufgrund von §eFehlverhalten §cvon einem Teammitglied gesperrt \n §eGrund: §4". $cfg->get("Grund"). "§7[§4". $config->get("Aktiv") ."§7] \n §eGebannt bis: §c" . $cfg->get("Dauer") ."(!) \n §eDu kannst einen Entbannungsantrag im §cForum §e stellen: \n §bhttps://revengermc.de/forum/",
									'buttons' => []
								];
								$pk = new ModalFormRequestPacket();
								$pk->formId = 73;
								$pk->formData = json_encode($data);
								$p->dataPacket($pk);
							}else{
								$cfg->set("Status", "Inaktiv");
								$cfg->save();
								$config->set("Aktiv", null);
								$config->save();
							}
						}
				}
			}
        $new  = scandir("/UniverseMC/AIOMod/Bans/");
        $tobc = array_diff($new, $this->oldreports);
        if ($tobc !== []) {
            foreach ($tobc as $repfile) {
                $rep = yaml_parse_file("/UniverseMC/AIOMod/Bans/" . $repfile);
                foreach (Server::getInstance()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("universe.notify")) {
                        foreach(Server::getInstance()->getOnlinePlayers() as $p){
										if(file_exists("/UniverseMC/Notify/".$p->getName().".yml")){
										$tcfg = new Config("/UniverseMC/Notify/".$p->getName().".yml", Config::YAML);
										if($tcfg->get("Notify") == true){
											$p->sendMessage("§7[§c§lAIOMod§r§7]§b< BAN §7| §c". $rep["Name"] ." §7| §c".$rep["Grund"] . "§7 | §c".$rep["Dauer"]." §7|§b>§r");
										}
									}
								}
                    }
                }

            }
        }
        $this->oldreports = $new;

    }
}
