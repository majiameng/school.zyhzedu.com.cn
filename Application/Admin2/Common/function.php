<?php

function setPassword($password){
	return md5(md5($password).'#$%');	
}
