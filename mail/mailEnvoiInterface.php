<?php
namespace mail;
interface mailEnvoiInterface
{
    public function envoi($dns,$from,$to,$subject,$content):void;
}