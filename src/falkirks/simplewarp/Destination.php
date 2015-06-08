<?php

namespace falkirks\simplewarp;


use falkirks\simplewarp\api\SimpleWarpAPI;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use shoghicp\FastTransfer\FastTransfer;

class Destination{
    /** @var Position  */
    protected $position;
    protected $address;
    protected $port;
    public function __construct(...$params){
        if(is_array($params[0])) $params = $params[0];
        if(isset($params[0])){
            if($params[0] instanceof Position){
                $this->position = $params[0];
            }
            else{
                if(isset($params[1])){
                    $this->address = $params[0];
                    $this->port = $params[1];
                }
                else{
                    throw new \BadMethodCallException;
                }
            }
        }
        else{
            throw new \BadMethodCallException;
        }
    }
    public function teleport(Player $player){
        if($this->position instanceof Position){
            //Server::getInstance()->getLogger()->info($this->position->x . " : " . $this->position->y . " : " . $this->position->z);
            $player->teleport($this->position);
        }
        else{
            $plugin = $player->getServer()->getPluginManager()->getPlugin("FastTransfer");
            if($plugin instanceof PluginBase && $plugin->isEnabled() && $plugin instanceof FastTransfer){
                $plugin->transferPlayer($player, $this->address, $this->port);
            }
            else{
                $player->getServer()->getPluginManager()->getPlugin("SimpleWarp")->getLogger()->warning("In order to use warps tp other servers, you must install " . TextFormat::AQUA . "FastTransfer" . TextFormat::RESET . ".");
                $player->sendPopup(TextFormat::RED . "Warp failed!" . TextFormat::RESET);
            }
        }
    }
    public function isInternal(){
        return $this->position instanceof Position;
    }

    /**
     * @return Position
     */
    public function getPosition(){
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getAddress(){
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getPort(){
        return $this->port;
    }
    public function toString(){
        if($this->isInternal()) {
            if($this->getApi()->getConfigItem("display-exact-coordinates")) {
                return "(X: {$this->getPosition()->x}, Y: {$this->getPosition()->y}, Z: {$this->getPosition()->z}, LEVEL: {$this->getPosition()->getLevel()->getName()}) ";
            }
            else{
                return "(X: {$this->getPosition()->getFloorX()}, Y: {$this->getPosition()->getFloorY()}, Z: {$this->getPosition()->getFloorZ()}, LEVEL: {$this->getPosition()->getLevel()->getName()}) ";
            }
        }
        return "(IP: {$this->getAddress()}, PORT: {$this->getPort()})";
    }

    /**
     * @return SimpleWarpApi
     */
    protected function getApi(){
        return Server::getInstance()->getPluginManager()->getPlugin("SimpleWarp")->getApi();
    }

}