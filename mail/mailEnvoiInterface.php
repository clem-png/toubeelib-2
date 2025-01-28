<?php

interface mailEnvoiInterface
{
    public function envoi($dns,$from,$to,$subject,$content):void;
}