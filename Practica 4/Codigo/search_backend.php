<?php
require_once('db/db.php');

class result{
	public $titulo;
	public $subtitulo;
	public function __construct($titulo, $subtitulo){
		$this->titulo = $titulo;
        $this->subtitulo = $subtitulo;
	}
}

function articleSearch($input){
	$list = [];
	$db = ConexionDB::getInstance();
	$result = $db->query("SELECT * FROM articulos WHERE (`titulo` LIKE '%".$input."%') OR (`subtitulo` LIKE '%".$input."%')");
	foreach($result->fetchAll() as $article) {
		$list[]=new result($article['titulo'],$article['subtitulo']);//array($article['titulo'] => $article['subtitulo']);
	}
	return $list;
}

	//sleep((float)(rand(200, 2000)) / 1000);

	echo json_encode(articleSearch($_POST["content"]));
?>
