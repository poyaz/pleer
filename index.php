<?php
$host = 'http://localhost:81/pleer';
require_once('pleer.php');
$pleer = new pleer('413677', 'E04GHISixvU9zzzsVoSl');
$pleer->run();
$pleer->getAccessToken();
$pleer->track_search('love', 1, 3);
$pleer->tracks_get_info('5738480NApX');
$pleer->tracks_get_lyrics('5738480NApX');
$pleer->tracks_get_download_link('5738480NApX');
$pleer->get_top_list(1, 1);
?>
