<!DOCTYPE html>
<html>
<head>
	
	<link rel="stylesheet"  type="text/css"  href="query.css"/>
	<title></title>
	
</head>
<body>
	<form name="formulario" method="post" action="">
		<label for="cardNo">Numero de tarjeta.</label>
		<input name="cardNo" type="number" placeholder="Numero de tarjeta"> 
		<label for="nombre">Nombre.</label>
		<input name="nombre" type="text" placeholder="Nombre"><br>
		Periodo
		<input name="fechaI" type="date"  label="Periodo">
		--------------
		<input name="fechaF" type="date" label="Periodo">
		<br>
		<button type="submit" value="Consulta" name="Consulta">Buscar</button> <button>Borrar</button><br>

	</form>
	<?php 
	//Se tiene que definir un dsn (acc2) en el odbc del sistema servidor y tiene que ser de su arquitectura
	$conn=odbc_connect('acc2', '', '');
	if ($conn) {
		echo "Conectado<br>";
	}
	else{
		echo "NO";
	}

	if(isset($_POST['Consulta'])){
			
			$nombre = $_POST['nombre'];
			$cardNo = $_POST['cardNo'];
			$fechaI = $_POST['fechaI'];
			//$horaI = $_POST['horaI'];	
			$fechaF = $_POST['fechaF'];
			//$horaF = $_POST['horaF'];										
	#Codigo para convertir la fecha del input a buscable en la bd (solo fecha)
	$fechaIF = date("m/d/Y", strtotime($fechaI));
	$fechaIF .= " 00:00:00";

	$fechaFF = date("m/d/Y", strtotime($fechaF));
	$fechaFF .= " 00:00:00";

		
if (empty($_POST["nombre"])) {
	if (empty($_POST["cardNo"])) {
		#Consulta con solo fechas
		$sql="SELECT acc_monitor_log.card_no, USERINFO.CardNo, acc_monitor_log.time, acc_monitor_log.event_point_name, acc_monitor_log.pin, acc_monitor_log.device_id, USERINFO.name, acc_monitor_log.device_id FROM acc_monitor_log  INNER JOIN USERINFO ON acc_monitor_log.[card_no] = USERINFO.[CardNo] WHERE acc_monitor_log.time Between #$fechaIF# AND #$fechaFF# ORDER BY time";
	}else{
		#Consulta solo numero de tarjeta y fecha
  		$sql="SELECT acc_monitor_log.card_no, USERINFO.CardNo,  acc_monitor_log.time, acc_monitor_log.event_point_name, acc_monitor_log.pin, acc_monitor_log.device_id, USERINFO.name FROM acc_monitor_log  INNER JOIN USERINFO ON acc_monitor_log.[card_no] = USERINFO.[CardNo] WHERE USERINFO.CardNo='$cardNo' AND acc_monitor_log.time Between #$fechaIF# AND #$fechaFF#";

	}
  	} else {
  		if (empty($_POST["cardNo"])) {
  			#consulta con solo nombre y fecha
    	$sql="SELECT acc_monitor_log.card_no, USERINFO.CardNo, acc_monitor_log.time, acc_monitor_log.event_point_name, acc_monitor_log.pin, acc_monitor_log.device_id, USERINFO.name, acc_monitor_log.device_id FROM acc_monitor_log  INNER JOIN USERINFO ON acc_monitor_log.[card_no] = USERINFO.[CardNo] WHERE USERINFO.name='$nombre' AND acc_monitor_log.time Between #$fechaIF# AND #$fechaFF# ORDER BY time";
  			}else{
		#Consulta con todos los campos llenos
    	$sql="SELECT acc_monitor_log.card_no, USERINFO.CardNo, acc_monitor_log.time, acc_monitor_log.event_point_name,acc_monitor_log.pin, acc_monitor_log.device_id, USERINFO.name FROM acc_monitor_log  INNER JOIN USERINFO ON acc_monitor_log.[card_no] = USERINFO.[CardNo] WHERE USERINFO.name='$nombre' AND USERINFO.CardNo='$cardNo' AND acc_monitor_log.time Between #$fechaIF# AND #$fechaFF#";
  			}	
  	}
	 	
		#echo "<p>".$sql."</p>";
		$result=odbc_exec($conn, $sql);
		?>
		<input  type="button" onclick="TableToExcel('Consulta', 'Registro de asistencia')" value="Descargar excel">
		<table align="left" id="Consulta" width="40%" border="1">
			<tr>
				<td>Tarjeta</td>
				<td>Nombre</td>			
				<td>Dia y hora</td>
				<td>Evento</td>	
			</tr>
					  
		<?php		
			while($row=odbc_fetch_array($result)){	
				$id=$row['CardNo'];
				$nombre=$row['name'];
				$fecha=$row['time'];
				$evento=$row['event_point_name'];
		?>  
				<tr>
					<td><?php echo "<p style='color:#000;'>".$id."</p>";?></td>	
					<td><?php echo "<p style='color:#000;'>".$nombre."</p>";?></td>
					<td><?php echo "<p style='color:#000;'>".$fecha."</p>";?></td>
					<td><?php if ($evento[-1]=='2') {
						echo "<p style='color:#0f0;'>Entrada</p>";#Si es 2
											}else{
												echo "<p style='color:#f00;'>Salida</p>";#Si es 1
											}?></td>
				</tr> 	<?php#date('"d/m/Y"',strtotime($row['time']))?>
			  
		<?php	
			}		
		?>

		</table>
		
<?php
	}

?>

<script src="TableToExcel.js"></script>
</body>
</html>