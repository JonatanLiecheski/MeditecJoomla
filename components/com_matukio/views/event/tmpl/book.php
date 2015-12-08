<?php

$idcatInscricao = 12; 
$idcatCursos = 13;
$idcatPalestras = 14;
$cont = 0;



$mainframe =& JFactory::getApplication('site');
$user =& JFactory::getUser();	
$db1 =& JFactory::getDBO();
$query1 = $db1->getQuery(true);
$document = JFactory::getDocument();


$db1->setQuery("SELECT b.name, b.email, a.catid, a.id, a.title
			FROM #__matukio AS a, #__matukio_bookings AS b 
			 WHERE
				(a.id = b.semid)
			   AND
				(b.userid = '$user->id')
			");

$result1 = $db1->loadObjectList();
foreach( $result1 as $obj )
{	
	if($obj->catid == $idcatInscricao)
	{
		$catInscricao = $obj->title;
	}
	$cont = $cont + 1;
}
//======================================
//neste proximo IF ele esta verificando se existe ainda nenhuma inscricao do usuario em seu banco de dados -- VERIFICAR ANO..
//======================================        

if(!empty($result1)){

	//======================================	
	// realizando consulta no banco para verificar se nao existe curso inscrito no mesmo horario.
	//======================================	

	$db2 =& JFactory::getDBO();
	$query2 = $db2->getQuery(true);
	$inicio = $this->event->begin;
	$fim =  $this->event->end;

	$db2->setQuery("SELECT a.id, a.catid, b.userid 
					FROM #__matukio AS a, #__matukio_bookings AS b 
					WHERE
						(a.id = b.semid)
					  AND
						(b.userid = '$user->id')
					  AND
						(a.catid != '$idcatInscricao')
					  AND
						((a.begin >= '$inicio' AND
						  a.begin <= '$fim')
					  OR
						 (a.end <= '$fim' AND
						  a.end >= '$inicio'))
				   ");

	$result2 = $db2->loadObjectList();



	//===================================
	//caso o ususario nao tenha alguma inscricao 
	//===================================

	if(empty($result2)){
		if($this->catid != $idcatInscricao && cont != 1 ){
		

		switch ($catInscricao){

			case "Pacote Master":

				$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

				$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

				$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
				. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath()
				. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
				. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";

			break;

			case "Pacote Completo":
				echo"Pacote completo";
				$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

				$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
				 . MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

				$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
				. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath()
				. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
				. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";
			break;

			case "Pacote Mini-curso":
				echo"Pacote MiniCursos";
				if($this->event->catid == $idcatCursos) {
					$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

					$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

					$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
					. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath()
					. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
					. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";
				}else{
					//erro somente minicurso
					$erro = 1;
				}
			break;

			case "Pacote Palestras":
				echo"Pacote Palestras";
				if($this->event->catid == $idcatPalestras) {

					$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

					$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

					$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
					. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath()
					. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
					. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";
				}else{
					//Erro somente palestras
					$erro = 1;
				}
			break;

			case "Pacote Mini-curso Individual":
				echo"Pacote Minicurso Individual";

				if($cont < 2){
					if($this->event->catid == $idcatCursos) {

						$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

						$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
						. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

						$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
						. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
						. MatukioHelperUtilsBasic::getComponentImagePath()
						. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
						. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";						
					}else{

						// erro somente minicurso
						$erro = 2;
					}	
				}else{
					// adicionar erro por ter mais de uma inscricao
					$erro = 3;
				}	
			break;

			case "Pacote Palestras Individual":
				echo"Pacote Palestras Individual";

				if($cont < 2){
					if($this->event->catid == $idcatPalestras) {

						$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

						$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
						. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

						$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
						. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
						. MatukioHelperUtilsBasic::getComponentImagePath()
						. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
						. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";						
					}else{
						//erro por nao ser um minicurso
						$erro = 2;
					}	
				}else{
					// adicionar erro por ter mais de uma inscricao
					$erro = 3;

				}	
			break;

			}
		}else{
			// proibido book em outro pacote inscricao
				$erro = 6;
			}




	}else{
		//proibida a inscricao em eventos simultaneos
		$erro = 4;	
	}

}


//================================================
//ele nao tem inscricao ainda.. liberar botao BOOK apenas para 
//eventos que sejam da categoria inscricoes...
//================================================

else{


	if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1) {
	
		if ($this->event->fees > 0) {
			$knopfunten .= " <input type=\"submit\" style=\"cursor:pointer;\" class=\"booking_button mat_button\" value=\""
		. JTEXT::_('COM_MATUKIO_BOOK_PAID') . "\">";
		} else {
			$knopfunten .= " <input type=\"submit\" style=\"cursor:pointer;\" class=\"booking_button mat_button\" value=\""
			. JTEXT::_('COM_MATUKIO_BOOK') . "\">";
		} 

	}else if($this->event->catid == $idcatInscricao) {

		$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":" . JFilterOutput::stringURLSafe($this->event->title));

		$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink . "\"><img src=\""
		. MatukioHelperUtilsBasic::getComponentImagePath() . "1132.png\" border=\"0\" align=\"absmiddle\"></a>";

		$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
		. "\"><span class=\"mat_button\" style=\"cursor:pointer;\"><img src=\""
		. MatukioHelperUtilsBasic::getComponentImagePath()
		. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
		. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";

	}else{ 
		//primeiramente deve haver alguma inscricao	
		$erro = 5;
	}

} 

switch ($erro){

	case 1:
		echo"<script type='text/javascript'>";
			echo "alert('Seu pacote de inscricao nao permite inscrições em Mini-Cursos!');";
		echo "</script>";
	break;

	case 2:
		echo"<script type='text/javascript'>";
			echo "alert('Seu pacote de inscricao nao permite inscrições em Palestras!');";
		echo "</script>";
	break;
	
	
	case 3:
		echo"<script type='text/javascript'>";
			echo "alert('Seu pacote de inscricao permite apenas uma inscricao!');";
		echo "</script>";
	break;
	
	case 4:
		echo"<script type='text/javascript'>";
			echo "alert('Proibida a inscricao em eventos simultaneos!');";
		echo "</script>";
	break;
	
	case 5:
		echo"<script type='text/javascript'>";
			echo "alert('Primeiramente e necessario efetuar uma inscricao de tipo pacote!');";
		echo "</script>";
	break;

	case 6:
		echo"<script type='text/javascript'>";
			echo "alert('voce nao pode ter duas inscricoes!');";
		echo "</script>";
	break;

}
?>