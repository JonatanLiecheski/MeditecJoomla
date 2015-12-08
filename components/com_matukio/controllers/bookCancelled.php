<?php

$idcatInscricao = 12;
$excluir = 0;
$cont = 0;
$mainframe =& JFactory::getApplication('site');
$user =& JFactory::getUser();	

$uid = JFactory::getApplication()->input->getInt('booking_id', 0);
$cid = JFactory::getApplication()->input->getInt('cid', 0);
$db1 =& JFactory::getDBO();
$query1 = $db1->getQuery(true);
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
	$cont = $cont + 1;
}

$result1 = $db1->loadObjectList();

foreach( $result1 as $obj )
{	

	if(($obj->id == $cid) AND ($obj->catid == $idcatInscricao) AND ($cont > 1)) 
	{
		$excluir = 1;	
	}


}


if($excluir == 1){
       
	echo"<script type='text/javascript'>";
		echo "alert('Voce nao pode cancelar esta inscricao, deve cancelar seus eventos!');";
	echo "</script>";

}else {
	
	$cid = JFactory::getApplication()->input->getInt('cid', 0);
        $uid = JFactory::getApplication()->input->getInt('booking_id', 0);



        if(!empty($cid)){
            $link = JRoute::_('index.php?option=com_matukio&view=event&id=' . $cid);
        } else {
            $link = JRoute::_('index.php?option=com_matukio&view=eventlist');
        }

        if (empty($cid) && empty($uid)) {
            $this->setRedirect($link, "COM_MATUKIO_NO_ID");
            return;
        }

        $msg = JText::_("COM_MATUKIO_BOOKING_ANNULATION_SUCESSFULL");

        $database = JFactory::getDBO();
        $user = JFactory::getuser();

        MatukioHelperUtilsEvents::sendBookingConfirmationMail($cid, $user->id, 2, true);

        if(!empty($uid)) {
            $database->setQuery("DELETE FROM #__matukio_bookings WHERE id = '" . $uid . "'");
        } else {
            if($user->id == 0) {
                JError::raiseError(403, "COM_MATUKIO_NO_ACCESS");
                return;
            } else {
                $database->setQuery("DELETE FROM #__matukio_bookings WHERE semid = " . $cid . " AND userid = '" . $user->id . "'");
            }
        }

        if (!$database->execute()) {
            JError::raiseError(500, $database->getError());
            $msg = JText::_("COM_MATUKIO_BOOKING_ANNULATION_FAILED") . " " . $database->getErrror();
        }

        $this->setRedirect($link, $msg);

        //sem_g001(1);
}
  
?>

