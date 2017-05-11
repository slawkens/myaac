<?php
	defined('MYAAC') or die('Direct access not allowed!');

	$player = $ots->createObject( 'Player' );
	$player->find($_GET['name']);
	if(function_exists('imagecreatefrompng'))
	{
		if($player->isLoaded())
		{
			$file = SIGNATURES_CACHE.$player->getId().'.png';
			if ( file_exists( $file ) and ( time( ) < ( filemtime($file) + ( 60 * $config['signature_cache_time'] ) ) ) )
			{
				header( 'Content-type: image/png' );
				readfile( SIGNATURES_CACHE.$player->getId().'.png' );
			}
			else
			{
				$image = imagecreatefrompng(SIGNATURES_BACKGROUNDS . 'signature.png');
				$color= imagecolorallocate($image , 255, 255, 255);
				imagettftext($image , 12, 0, 20, 32, $color, SIGNATURES_FONTS . 'font.ttf' , 'Name:');
				imagettftext($image , 12, 0, 70, 32, $color, SIGNATURES_FONTS . 'font.ttf' , $player->getName());

				imagettftext($image , 12, 0, 20, 52, $color, SIGNATURES_FONTS . 'font.ttf' , 'Level:');
				imagettftext($image , 12, 0, 70, 52, $color, SIGNATURES_FONTS . 'font.ttf' , $player->getLevel() . ' ' . $config['vocations'][$player->getVocation()]);

				$rank = $player->getRank();
				if($rank->isLoaded())
				{
					imagettftext($image , 12, 0, 20, 75, $color, SIGNATURES_FONTS . 'font.ttf' , 'Guild:');
					imagettftext($image , 12, 0, 70, 75, $color, SIGNATURES_FONTS . 'font.ttf' , $player->getRank()->getName() . ' of the ' . $$rank->getGuild()->getName());
				}
				imagettftext($image , 12, 0, 20, 95, $color, SIGNATURES_FONTS . 'font.ttf' , 'Last Login:');
				imagettftext($image , 12, 0, 100, 95, $color, SIGNATURES_FONTS . 'font.ttf' , (($player->getLastLogin() > 0) ? date("j F Y, g:i a", $player->getLastLogin()) : 'Never logged in.'));
				imagepng($image, SIGNATURES_CACHE . $player->getID() . '.png');
				imagedestroy($image);

				header('Content-type: image/png');
				readfile(SIGNATURES_CACHE . $player->getId().'.png');
			}
		}
		else
		{
			header('Content-type: image/png');
			readfile(SIGNATURES_IMAGES . 'nocharacter.png');
		}
	}
	else
	{
		header('Content-type: image/png');
		readfile(SIGNATURES_IMAGES . 'nogd.png');
	}
?>