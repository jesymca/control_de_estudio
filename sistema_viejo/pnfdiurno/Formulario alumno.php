<?php 
session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
} else {
	header("Location: index.html");
	exit;
}
$now = time();
if($now > $_SESSION['expire']) {
	session_destroy();
	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
	exit;
}

include 'menu.php';
date_default_timezone_set('America/Caracas');
$fecha = date('d-m-Y');
$año = date('Y');
include "db.php";

$query = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$usuario = $row['nombre'];
}  		
?>

<style type="text/css">

	#marco
	{
		width:900px;
		min-width: 900px;
	}
	.col-md-12 {
		padding-left:30px; 
		padding-right:30px;
	}
	
	table{		
		width:880px;		
	}
	input[type="text"]{
		background: white;
	}
</style>

<div class="container" id="marco">

	<fieldset>
		<div class="form-group" id="titulo_formulario"> 
			<label id="titulo_formulario"><span class="glyphicon glyphicon-barcode"></span> Inscripcion del Alumno</label>
			<input id="fecha_actual" type="hidden" value="<?php echo $año?>">
		</div>
		<br><br>			

		<form class="form-horizontal" method="post" action="Formulario alumno_insert.php">

			<!-- Button -->
			<table>
				<tr>
					<td style="width: 500px">		
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<input type="submit" class="btn btn-primary" value="Guardar" style="width:100px"/>
								<!-- <a id="Actualizar" name="Actualizar" class="btn btn-primary" style="background: #0C4783;width:130px"><span class="glyphicon glyphicon-edit"></span> Actualizar</a> -->
								<a href="SERVICIOS3.php" class="btn btn-primary" style="width:130px"><span class="glyphicon glyphicon-print"></span> Imprimir</a>
								<a href="principal.php" class="btn btn-primary" style="width:100px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>

							</div>
						</div>
					</td>
					<td style="width: 10px"></td>
					<td style="width: 200px;">	

						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:-25px;">
								<label for="buscar">Buscar alumno</label> 
								<input id="buscar" name="buscar" type="text" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 130px">
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: -12%;margin-top:0px;">
								<a id="btnBuscar" class="btn btn-primary" style="width:130px"><span class="glyphicon glyphicon-search"></span> Buscar</a>
								<input id="id" name="id" type="hidden">
							</div>
						</div>
					</td>
				</tr>
			</table>

			<table style="border: 1px">
				<tr>
					<td style="width: 2%">					  
						<div class="form-group">
							<div class="col-md-12" style="width: 200px;margin-left: 0%;">								
								<label for="cedula">Cedula</label> 
								<input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control" required pattern="[V|E][0-9]{7,9}">
							</div>
						</div>
					</td>

					<td style="width: 30%">  
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px">
								<label for="nombre">Nombre</label>  
								<input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control" required>
							</div>
						</div>
					</td>

					



						<td style="width: 10%"> 
						<div class="form-group">
							<div class="col-md-12" style="margin-left:-10px;margin-top:-33px">
								<label for="grado">Grado</label>					
						        <input id="grado" name="grado" type="hidden" required>

								<div class="selector-grado">   
									<select style="width:70%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
									    <option value="">Seleccionar</option>
									    <option value=T>Tsu</option>
				                        <option value=L>Licenciado</option>            
				                        <option value=I>Ingeniero</option>
			                        </select>                         
					          </div>
							</div>
						</div>
					</td>

					<td style="width: 10%">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: -10px;margin-top:-33px;">
								<label for="tipingreso">Tipo de ingreso</label>    
								<input type="hidden" id="tipingreso" name="tipingreso">
								<div class="selector-tipingreso">   
									<select style="width:71%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
										<option value="APROBO. INTRODUCTORIO">APROBO. INTRODUCTORIO</option>
										<option value="AUTORIZADO DIRECCION">AUTORIZADO DIRECCION</option>
										<option value="AUTORIZADO SUBDIR. ACAD">AUTORIZADO SUBDIR. ACAD</option>
										<option value="AUTORIZADO SUBDIR.ADMIN">AUTORIZADO SUBDIR.ADMIN</option>
										<option value="AYUDA SOCIAL">AYUDA SOCIAL</option>
										<option value="CENSO">CENSO</option>
										<option value="OPSU">OPSU</option>
										<option value="CONVENIO">CONVENIO</option>
										<option value="CULTURA">CULTURA</option>
										<option value="DEPORTE">DEPORTE</option>
										<option value="MISION SUCRE">MISION SUCRE</option>
										<option value="PROPEDEUTICO">PROPEDEUTICO</option>
										<option value="RENDIMIENTO">RENDIMIENTO</option>
									</select>      
								</div> 
							</div>
						</div>
					</td>
				</tr>
			</table>			

			<table>
				<tr>		
					<td style="width: 12%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-top:-5px">
								<label for="ingreso">Ingreso</label>  
								<input id="ingreso" name="ingreso" type="text" class="form-control" value="<?php echo $fecha?>" readonly style="background: white">
							</div> 
						</div>
					</td>  
<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-35px">
								<label for="pnf">Pnf</label>  
								<input id="pnf" name="pnf" type="hidden">
								<div class="selector-pnf">   
									<select style="width:70%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
										<option value="0">Pnf</option>
										<option value="1">Tradicional</option>
										<option value="2">Prosecución</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td>
					<!-- <td style="width: 15%;padding-top: 0px;padding-right: 20px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-35px">
								<label for="semestre">Semestre o Trimestre</label>  
								<input id="semestre" name="semestre" type="hidden">

								<div class="selector-semestre">   
									<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="0">0</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
									</select>      
								</div> 

							</div>
						</div>
					</td> -->

					<td style="width: 20%">  
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0px;margin-top:-35px">
								<label for="carrera">Carrera</label>    
								<input id="carrera" name="carrera" type="hidden">
								<div class="selector-carrera">   
									<select style="width:78%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
								</div> 
							</div>
						</div>
					</td>

					<td style="width: 10%"> 
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-35px">
								<label for="sexo">Sexo</label>  
								<input id="sexo" name="sexo" type="hidden">						

								<div class="selector-sexo">   
									<select style="width:60%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
										<option value="Masculino">Masculino</option>
										<option value="Femeninos">Femeninos</option>										
									</select>      
								</div> 
							</div>
						</div>
					</td>

					<td style="width: 15%;padding-top: 0px;padding-right: 20px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-35px">
								<label for="edocivil">Estado civil</label>  
								<input id="edocivil" name="edocivil" type="hidden">

								<div class="selector-edocivil">   
									<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
										<option value="Casado">Casado</option>
										<option value="Soltero">Soltero</option>
										<option value="Divorciado">Divorciado</option>																				
									</select>      
								</div>
							</div>
						</div>
					</td>

				</tr>
			</table>	

			<table style="margin-top:-10px">
				<tr>					

					<td style="width: 15%;padding-top: 10px;padding-right: 20px;padding-bottom:40px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0px;margin-top:0px">
								<label for="estado">Estado</label>  
								<input id="estado" name="estado" type="hidden">
								<div class="selector-estado"> 									
									<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>
								</div>								
							</div>
						</div>							
					</td>

					<td style="width: 35%;padding-top: 10px;padding-right: 20px;padding-bottom:40px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px">
								<label for="lugar">Lugar de nacimiento</label>  
								<input id="lugar" name="lugar" type="hidden">
								<div class="selector-lugar"> 									
									<select style="width:88%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>
								</div>								
							</div>
						</div>
					</td>
					
					<td style="width: 20%;padding-top: 10px;padding-right: 20px;padding-bottom:40px;">				
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0px;margin-top:0px">
								<label for="municipio">Municipio</label>  
								<input id="municipio" name="municipio" type="hidden">

								<div class="selector-municipios"> 									
									<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>
								</div>

							</div>
						</div> 
					</td>

					<td style="width: 15%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">									
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-30px">
								<label for="procedenci">Parroquias</label>  
								<input id="procedenci" name="procedenci" type="hidden">

								<div class="selector-parroquias"> 									
									<select style="width:68%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>
								</div>
							</div>
						</div>							
					</td>
				</tr>
			</table>

			<table>
				<tr>				

					<td style="width: 20%">	
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px;">
								<label for="fechanac">Fecha de nacimiento</label>  
								<input id="fechanac" name="fechanac" type="text" placeholder="Fechanac" class="form-control" required pattern="[0-9]{2}[-][0-9]{2}[-][0-9]{4}">
							</div>
						</div>
					</td>

					<td style="width: 10%">					
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px;margin-left: 0px">
								<label for="edad">Edad</label>  
								<input id="edad" name="edad" type="text" placeholder="Edad" class="form-control" readonly style="background: white">
							</div>
						</div>
					</td>					
					<td style="width: 20%">	

						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px">
								<label for="email">Correo</label>  
								<input id="email" name="email" type="text" placeholder="Correo@pagina.com" class="form-control">
							</div>
						</div>
					</td>
					<td style="width: 40%">	 
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:0px">
								<label for="direccion">Direccion</label>  
								<input id="direccion" name="direccion" type="text" placeholder="Direccion" class="form-control">
							</div>
						</div>
					</td>
				</tr>
			</table>
			
			<table>
				<tr>				

					<td style="width: 20%">	
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="telefonoh">Telefono hogar</label>  
								<input id="telefonoh" name="telefonoh" type="text" placeholder="Telefono hogar" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 20%">	
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="telefonoc">Telefono celular</label>  
								<input id="telefonoc" name="telefonoc" type="text" placeholder="Telefono celular" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 20%">	
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="telefonot">Telefono familiar</label>  
								<input id="telefonot" name="telefonot" type="text" placeholder="Telefono familiar" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 20%">	
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="egreso">Egreso</label>  
								<input id="egreso" name="egreso" type="text" placeholder="Egreso" class="form-control" pattern="[0-9]{4}[-][1-3]{1}" title="2020-1">
							</div>
						</div>
					</td>
				</tr>
			</table>

			<table>
				<tr>

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">	
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:-5px">
								<label for="pasantia">Pasantia</label>  
								<input id="pasantia" name="pasantia" type="text" placeholder="Pasantia" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 0px;padding-right: 20px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 85%;margin-left: 0%;margin-top:-35px">
								<label for="turno">Turno</label>  
								<input id="turno" name="turno" type="hidden">
								<div class="selector-turno">   
									<select style="width:90%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Dia">Dia</option>										
										<option value="Noche">Noche</option>																				
									</select>      
								</div>
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 10px;padding-right: 20px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 90%;margin-left: 0%;margin-top:-40px">
								<label for="trabajo">Trabaja</label>  
								<input id="trabajo" name="trabajo" type="hidden">
								<div class="selector-trabajo">   
									<select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Si">Si</option>
										<option value="No">No</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 10px;padding-right: 20px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 90%;margin-left: 0%;margin-top:-40px">
								<label for="beca">Beca</label>  
								<input id="beca" name="beca" type="hidden">
								<div class="selector-beca">   
									<select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Si">Si</option>
										<option value="No">No</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td>

					<td style="width: 15%;padding-top: 10px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:-15px">
								<label for="ireceptor">Receptor de Documentos</label>                                           
								<input id="ireceptor" name="ireceptor" type="text" class="form-control" value="<?php echo $usuario?>" style="background: white" required readonly>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>					

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-5px">
								<label for="folio">Folio</label>  
								<input id="folio" name="folio" type="text" placeholder="Folio" class="form-control">
							</div>
						</div>
					</td>
					<td style="width: 10%;padding-top: 0px;padding-right:0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-5px">
								<label for="tomo">Tomo</label>  
								<input id="tomo" name="tomo" type="text" placeholder="Tomo" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-5px">
								<label for="rusnies">Rusnies</label>  
								<input id="rusnies" name="rusnies" type="text" placeholder="Rusnies" class="form-control">
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:0px;">
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 0%;margin-top:-35px">
								<label for="discapacid">Discapacidad</label>  
								<input id="discapacid" name="discapacid" type="hidden">
								<div class="selector-discapacid">   
									<select style="width:70%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Si">Si</option>
										<option value="No">No</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td>

					
				</tr>
			</table>

			<table>
				<tr>
					<!-- <td style="width: 10%;padding-top: 0px;padding-right:0px;padding-bottom:30px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="trayecto" style="width: 200px">Trayecto</label>  
								<input id="trayecto" name="trayecto" type="hidden">
								<div class="selector-trayecto">   
									<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="0">TRAYECTO 0</option>
										<option value="1">TRAYECTO 1</option>
										<option value="2">TRAYECTO 2</option>
										<option value="3">TRAYECTO 3</option>
										<option value="4">TRAYECTO 4</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td> -->

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="fcedula" style="width: 200px">1. F.Cedula de Identidad</label>  
								<input id="fcedula" name="fcedula" type="hidden">
								<div class="selector-fcedula">   
									<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Si">Si</option>
										<option value="No">No</option>																														
									</select>      
								</div>
							</div>
						</div>
					</td>

					<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
								<label for="inscripmilt" style="width: 200px">6. F.Inscripcion Militar</label>  
								<input id="inscripmilt" name="inscripmilt" type="hidden">
								<div class="selector-inscripmilt">   
									<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
										<option value="">Seleccionar</option>
										<option value="Si">Si</option>
										<option value="No">No</option>									</select>      
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<table>
					<tr> 
						<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
									<label for="ftitulo" style="width: 200px">2. F.Titulo de Bachiller</label>  
									<input id="ftitulo" name="ftitulo" type="hidden">
									<div class="selector-ftitulo">   
										<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
											<option value="">Seleccionar</option>
											<option value="Si">Si</option>
											<option value="No">No</option>									</select>      
										</div>
									</div>
								</div>
							</td>         
							<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
								<div class="form-group">
									<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
										<label for="fcerfidicado" style="width: 200px">7. F.Certificado de Salud</label>  
										<input id="fcerfidicado" name="fcerfidicado" type="hidden">
										<div class="selector-fcerfidicado">   
											<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
												<option value="">Seleccionar</option>
												<option value="Si">Si</option>
												<option value="No">No</option>									</select>      
											</div>
										</div>
									</div>
								</td>

								<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
									<div class="form-group">
										<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
											<label for="fnotas" style="width: 200px">3. F.Notas Certificadas</label>  
											<input id="fnotas" name="fnotas" type="hidden">
											<div class="selector-fnotas">   
												<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
													<option value="">Seleccionar</option>
													<option value="Si">Si</option>
													<option value="No">No</option>
												</select>      
											</div>
										</div>
									</div>
								</td>
							</tr>
						</table>

						<table>
							<tr> 
								<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">
									<div class="form-group">
										<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
											<label for="fdosfotos" style="width: 200px">8. Dos Fotos Tipo Carnet</label>  
											<input id="fdosfotos" name="fdosfotos" type="hidden">
											<div class="selector-fdosfotos">   
												<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
													<option value="">Seleccionar</option>
													<option value="Si">Si</option>
													<option value="No">No</option>									</select>      
												</div>
											</div>
										</div>
									</td>

									<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">       
										<div class="form-group">
											<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
												<label for="fpinscrip" style="width: 200px">4. F.Planilla de Inscripcion CNU</label>  
												<input id="fpinscrip" name="fpinscrip" type="hidden">
												<div class="selector-fpinscrip">   
													<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
														<option value="">Seleccionar</option>
														<option value="Si">Si</option>
														<option value="No">No</option>									</select>      
													</div>
												</div>
											</div>
										</td>
										<td style="width: 10%;padding-top: 0px;padding-right: 0px;padding-bottom:30px;">       
											<div class="form-group">
												<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
													<label for="fnacimie" style="width: 200px">9. F.Partida de Nacimiento</label>  
													<input id="fnacimie" name="fnacimie" type="hidden">
													<div class="selector-fnacimie">   
														<select style="width:81%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
															<option value="">Seleccionar</option>
															<option value="Si">Si</option>
															<option value="No">No</option>
														</select>      
													</div>
												</div>
											</div>
										</td>			
									</tr>
								</table>
								<br>
							</form>
						</fieldset>
					</div>
				</form>

				<script src="Formulario alumno.js"></script>

				<script type="text/javascript">
					$(document).ready(function() {

						$.ajax({
							type: "POST",
							url: "getpensum.php",
							success: function(response)
							{
								$('.selector-carrera select').html(response).fadeIn();
							}
						});

						$('.selector-carrera select').change(function(){					
							var v = $(this).val(); 			
							$('#carrera').val(v);
						});

						$.ajax({
							type: "POST",
							url: "getestados.php",
							success: function(response)
							{				
								$('.selector-estado select').html(response).fadeIn();
							}
						});

						$('.selector-estado select').change(function(){					
							var v = $(this).val(); 
							var datos = v.split('|');
							buscarciudades(datos[0]);	
							$('#estado').val(datos[1]);
						});

						function buscarciudades(val1){        
							var parametros = {
								"estado":val1
							}
							$.ajax({
								data:parametros,
								type: "POST",
								url: "getciudades.php",
								success: function(response)
								{
									$('.selector-lugar select').html(response).fadeIn();
								}
							});        
						}

						$(".selector-lugar select").change(function(){			
							var v = $(this).val();
							var datos = v.split('|');
							buscarmunicipios(datos[0]);
							$('#lugar').val(datos[1]);		 	
						});

						function buscarmunicipios(val1){        
							var parametros = {
								"estado":val1
							}
							$.ajax({
								data:parametros,
								type: "POST",
								url: "getmunicipios.php",
								success: function(response)
								{
									$('.selector-municipios select').html(response).fadeIn();
								}
							});        
						}

						$(".selector-municipios select").change(function(){
							var v = $(this).val(); 
							var datos = v.split('|');
							buscarparroquias(datos[0]);
							$('#municipio').val(datos[1]);			
						});

						function buscarparroquias(val1){  			
							var parametros = {
								"municipio":val1
							}
							$.ajax({
								data:parametros,
								type: "POST",
								url: "getparroquias.php",
								success: function(response)
								{
									$('.selector-parroquias select').html(response).fadeIn();
								}
							});        
						}

						$('.selector-parroquias select').change(function(){			
							var v = $(this).val(); 			
							$('#procedenci').val(v);				
						});

						$('.selector-grado select').change(function(){			
							var v = $(this).val(); 			
							$('#grado').val(v);				
						});

						

						$('.selector-sexo select').change(function(){		
							$('#sexo').val($(this).val());				
						});

						$('.selector-edocivil select').change(function(){		
							$('#edocivil').val($(this).val());				
						});

						$('.selector-turno select').change(function(){		
							$('#turno').val($(this).val());				
						});

						$('.selector-tipingreso select').change(function(){		
							$('#tipingreso').val($(this).val());				
						});
						$('.selector-semestre select').change(function(){		
							$('#semestre').val($(this).val());				
						});

						$('.selector-trabajo select').change(function(){		
							$('#trabajo').val($(this).val());				
						});

						$('.selector-beca select').change(function(){		
							$('#beca').val($(this).val());				
						});

						$('.selector-discapacid select').change(function(){		
							$('#discapacid').val($(this).val());				
						});

						$('.selector-pnf select').change(function(){		     	
							$('#pnf').val($(this).val());				
						});

						$('.selector-trayecto select').change(function(){		
							$('#trayecto').val($(this).val());				
						});

						$('.selector-fcedula select').change(function(){		
							$('#fcedula').val($(this).val());				
						});

						$('.selector-inscripmilt select').change(function(){		
							$('#inscripmilt').val($(this).val());				
						});	

						$('.selector-ftitulo select').change(function(){		
							$('#ftitulo').val($(this).val());				
						});	

						$('.selector-fcerfidicado select').change(function(){		
							$('#fcerfidicado').val($(this).val());				
						});	

						$('.selector-fnotas select').change(function(){		
							$('#fnotas').val($(this).val());				
						});	

						$('.selector-fdosfotos select').change(function(){		
							$('#fdosfotos').val($(this).val());				
						});

						$('.selector-fpinscrip select').change(function(){		
							$('#fpinscrip').val($(this).val());				
						});

						$('.selector-fnacimie select').change(function(){		
							$('#fnacimie').val($(this).val());				
						});

						$("#btnBuscar").click(function(){
							var valor = $('#buscar').val();
							obten_datos(valor);
						});

						$("#Actualizar").click(function(){
							var id = $('#id').val();
							var codigo = $('#codigo').val();
							var cedula = $('#cedula').val();
							var nombre = $('#nombre').val();
							var grado = $('#grado').val();
							var carrera = $('#carrera').val();
							var mencion = $('#mencion').val();
							var plan = $('#plan').val();
							var actividad = $('#actividad').val();
							var sexo = $('#sexo').val();
							var edocivil = $('#edocivil').val();
							var lugar = $('#lugar').val();
							var municipio = $('#municipio').val();
							var estado = $('#estado').val();
							var procedenci = $('#procedenci').val();
							var fechanac = $('#fechanac').val();
							var edad = $('#edad').val();
							var direccion = $('#direccion').val();
							var telefonoh = $('#telefonoh').val();
							var telefonoc = $('#telefonoc').val();
							var telefonot = $('#telefonot').val();
							var email = $('#email').val();
							var tipingreso = $('#tipingreso').val();
							var ingreso = $('#ingreso').val();
							// var semestre = $('#semestre').val();
							var egreso = $('#egreso').val();
							var pasantia = $('#pasantia').val();
							var turno = $('#turno').val();
							var trabajo = $('#trabajo').val();
							var beca = $('#beca').val();
							var rusnies = $('#rusnies').val();
							var ireceptor = $('#ireceptor').val();
							var discapacid = $('#discapacid').val();
							var tomo = $('#tomo').val();
							var folio = $('#folio').val();
							var pnf = $('#pnf').val();
							// var trayecto = $('#trayecto').val();
							var fcedula = $('#fcedula').val();
							var inscripmilt = $('#inscripmilt').val();
							var ftitulo = $('#ftitulo').val();
							var fcerfidicado = $('#fcerfidicado').val();
							var fnotas = $('#fnotas').val();
							var fdosfotos = $('#fdosfotos').val();
							var fpinscrip = $('#fpinscrip').val();
							var fdepbanc = $('#fdepbanc').val();
							var fnacimie = $('#fnacimie').val();
							$.post("Formulario alumno_update.php",{accion: "Actualizar",id:id,codigo:codigo,cedula:cedula,nombre:nombre,grado:grado,carrera:carrera,mencion:mencion,plan:plan,actividad:actividad,sexo:sexo,edocivil:edocivil,lugar:lugar,municipio:municipio,estado:estado,procedenci:procedenci,fechanac:fechanac,edad:edad,direccion:direccion,telefonoh:telefonoh,telefonoc:telefonoc,telefonot:telefonot,email:email,tipingreso:tipingreso,ingreso:ingreso,egreso:egreso,pasantia:pasantia,turno:turno,trabajo:trabajo,beca:beca,rusnies:rusnies,ireceptor:ireceptor,discapacid:discapacid,tomo:tomo,folio:folio,pnf:pnf,fcedula:fcedula,inscripmilt:inscripmilt,ftitulo:ftitulo,fcerfidicado:fcerfidicado,fnotas:fnotas,fdosfotos:fdosfotos,fpinscrip:fpinscrip,fdepbanc:fdepbanc,fnacimie:fnacimie},function(res){
								swal(res);
							}); 
						});

						$('#selector-tipingreso').change(function(){		   		
							var v = $(this).val();			
							$('#lugar').val(v);
						});


						$('#fechanac').change(function(){
							var v = $('#fechanac').val();
							var fec = $('#fecha_actual').val();			
							calcular_edad(v,fec);
						});

						function calcular_edad(val1,val2){
							var v = val1;  
							var v2 = val2;
							$('#edad').val(v2-v.substring(6,10));                                     
						}

						$('#cedula').change(function(){
							var v = $('#cedula').val();			
							buscar_cedula(v);
						});

						function buscar_cedula(val1){  			
							var parametros = {
								"cedula":val1
							}
							$.ajax({
								data:parametros,
								type: "POST",
								url: "getcedula_alumno.php",
								success: function(response)
								{
									if(response=="Ya existe"){						
										document.getElementById('cedula').style.background="red";						
										document.getElementById('cedula').focus();                        
										swal(response);
									}else{
										document.getElementById('cedula').style.background="White";
									}
								}
							});        
						}
					});

				</script>
			</body>
			</html>
