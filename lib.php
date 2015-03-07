	<!-- start tinymce -->
	<script src="tinymce/tinymce.min.js"></script>
	<script>tinymce.init({selector:'textarea',
	plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern"
    ]
	});</script>
	<!-- end tinymce -->
	
	<!-- start datepicker -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.1/css/datepicker.css" rel="stylesheet">	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.1/js/bootstrap-datepicker.js"></script>
    <!-- end datepicker -->
<?php 
$host="localhost";
$user="root";
$password="";
$db="sekolah";
mysql_connect ($host,$user,$password);
mysql_select_db($db);
$filename=basename($_SERVER["SCRIPT_NAME"]);
//membuat function untuk crud (create read update delete)

function default_table(){
	global $db;
	$rst=mysql_query("show tables from $db");
	return mysql_fetch_array($rst)[0];
}
function default_level(){
	global $db;
	$level=array();
	$rst=mysql_query("show tables from $db");
    while($fd=mysql_fetch_array($rst)){
		$rsf=mysql_query("SHOW COLUMNS FROM ".$fd[0]);
		while($fc=mysql_fetch_array($rsf)){
			if($fc[0]=='username'){
			$level[]=$fd[0];
			}
		}
    }
	return $level;
}

if(!isset($_REQUEST['table'])){$table=default_table();}else{$table=$_REQUEST['table'];}
if(!isset($_REQUEST['mode'])){$mode="view";}else{$mode=$_REQUEST['mode'];}

function crud(){
	global $filename;
	global $table;
	global $mode;
	switch($mode){
		case "view":
				if(!isset($_REQUEST['page'])){$page=1;$page_next=2;$page_prev=null;$page_max=3;}else{$page=$_REQUEST['page'];$page_next=$_REQUEST['page']+1;$page_prev=$_REQUEST['page']-1;}
				    if($page>=3){$page_min=$page-1;}else{$page_min=1;}
					$page_max=$page+1;
					$rs=mysql_query("select * from $table");			
					$max_item=20;
					$sum_item=mysql_num_rows($rs);
					$sum_page=ceil($sum_item/$max_item);
					if(($page+1)>=$sum_page){$page_max=$sum_page;$page_next=null;}else{$page_max=$page_min+2;}
					$limit=($page-1)*$max_item;
					$limit=$limit.",".$max_item;
					$rs=mysql_query("select * from $table limit $limit ");

			echo "<div class='panel panel-default' style='width:100%;overflow:auto;'>";
			echo "<a href='index.php?table=$table&mode=new' onclick=";?>"return confirm('Benarkah hendak menambah data baru?')"<?php echo "><img src='images/add.png'></a>";
			echo "<table border=1><tr><th>Action</th>";
			for($i=0;$i<mysql_num_fields($rs);$i++){
				echo "<th>".mysql_field_name($rs,$i)."</th>";
			}
			echo "</tr>";
			while($row=mysql_fetch_array($rs)){
				echo "<tr><td><a  href='index.php?table=$table&mode=edit&id_$table=$row[0]' onclick=";?>"return confirm('Benarkah hendak mengedit?')"<?php echo "><img src='images/edit.png'></a>  <a href='index.php?table=$table&mode=delete&id_$table=$row[0]' onclick=";?>"return confirm('Benarkah hendak menghapus?')"<?php echo "><img src='images/delete.png'></a></td>";
				for($i=0;$i<mysql_num_fields($rs);$i++){
				echo "<td>";
				/*echo "<td>".$row[mysql_field_name($rs,$i)]."</td>";*/
				/////////////////////////
									switch(mysql_field_type($rs,$i)){
					case "int":
					    if(mysql_field_len($rs,$i)==11){
							//INT SMALL
								if($i!=0 && substr(mysql_field_name($rs,$i),0,3)=='id_'){
								$rs2=mysql_query("select * from ".substr(mysql_field_name($rs,$i),3)." where ".mysql_field_name($rs,$i)."='".$row[mysql_field_name($rs,$i)]."'");
								while($row2=mysql_fetch_array($rs2)){
									echo $row2[1];
								}
							}
							else{
								echo $row[mysql_field_name($rs,$i)];
							}

							
						}
						elseif(mysql_field_len($rs,$i)==20){
							//INT BIG
							echo $row[mysql_field_name($rs,$i)];
						}
						else{
							//INT BIG
							echo $row[mysql_field_name($rs,$i)];
						}
						break;
					case "string":
					    //STRING
					    echo $row[mysql_field_name($rs,$i)];
						break;
					case "date":
						//TANGGAL
						if($row[mysql_field_name($rs,$i)]==null){
							echo "";
						}
						else{
							echo date('d-m-Y',strtotime($row[mysql_field_name($rs,$i)]));
						}
						break;
					case "blob":
						if(strpos(mysql_field_flags($rs,$i),"binary")){
						//GAMBAR
					    echo "<img src='data:image/jpg;base64,".base64_encode($row[mysql_field_name($rs,$i)]). "' class='img-responsive img-thumbnail' width=48>";
						}
						else{
						//TEXT
							echo $row[mysql_field_name($rs,$i)];
						}
					break;
					default:
						echo $row[mysql_field_name($rs,$i)];
						break;
					}
                  echo "</td>";
				/////////////////////////
			    }
				echo "</tr>";
			}
			echo "</table>
						</div>";
							echo "					<div class='art-pager'>
				<span";if($page_prev==null){echo " class='disabled'";}echo ">";if($page_prev==null){echo "<a href='#'>Prev</a>";}else{echo "<a href='".$filename."?table=".$table."&mode=view&page=$page_prev'>Prev</a>";}echo "</span>";
		        for($i=$page_min;$i<=$page_max;$i++){		
				echo "<span";if($page==$i){echo " class='active'";}echo "><a href='".$filename."?table=".$table."&mode=view&page=$i'>$i</a></span>";
				}
				echo "
				<span";if($page_next==null){echo " class='disabled'";}echo ">";if($page_next==null){echo "<a href='#'>Next</a>";}else{echo "<a href='".$filename."?table=".$table."&mode=view&page=$page_next'>Next</a>";}echo "</span>
				</div>";
		break;
		case "new": 
		$rs=mysql_query("select * from $table");
		echo "<form action='$filename?table=$table&mode=save' class='form-horizontal'method=post enctype='multipart/form-data'>";
		//echo "<table border=1>";
		echo "<fieldset>";
			
				
				for($i=0;$i<mysql_num_fields($rs);$i++){
				  //echo "<tr><td>".str_replace("_"," ",mysql_field_name($rs,$i))."</td><td>";
				  echo "<p><label><b>".str_replace("_"," ",mysql_field_name($rs,$i))."</b></label><br>";
				  /*echo "<tr><td>".str_replace("_"," ",mysql_field_name($rs,$i))."</td><td><input type=text name='".mysql_field_name($rs,$i)."' size='".mysql_field_len($rs,$i)."'></td></tr>";*/
				  ///////////////////////////////////////////////////////////
				  					switch(mysql_field_type($rs,$i)){
					case "int":
					    if(mysql_field_len($rs,$i)==11){
							//INT SMALL
							if($i!=0 && substr(mysql_field_name($rs,$i),0,3)=='id_'){
								echo "<select name='".mysql_field_name($rs,$i)."'>";
								$rs2=mysql_query("select * from ".substr(mysql_field_name($rs,$i),3));
								while($row2=mysql_fetch_array($rs2)){
									echo "<option value='".$row2[0]."'>".$row2[1]."</option>";
								}
								echo"</select>";
							}
							else{
									echo "<input type=text name=".mysql_field_name($rs,$i)." size=11>";
							}
						}
						elseif(mysql_field_len($rs,$i)==20){
							//INT BIG
									echo "<input type=text name=".mysql_field_name($rs,$i)." size=20>";
						}
						else{
							//INT BIG
									echo "<input type=text name=".mysql_field_name($rs,$i)." size=3>";
						}
						break;
					case "string":
					    //STRING
							if(mysql_field_flags($rs,$i)=="enum"){
								$rsenum=mysql_query("SELECT column_type FROM information_schema.columns WHERE table_name = '$table' AND column_name = '".mysql_field_name($rs,$i)."'");
								$rowenum=mysql_fetch_array($rsenum);$listenum=substr($rowenum['column_type'],6);$listenum=substr($listenum,0,-2);$listenum=explode("','",$listenum);
								if(count($listenum)<=2){
									echo "<input type=radio name='".mysql_field_name($rs,$i)."' value='".$listenum[0]."'> ".$listenum[0]."   <input type=radio name='".mysql_field_name($rs,$i)."' value='".$listenum[1]."'> ".$listenum[1];
									}
								else{
									echo "<select name=".mysql_field_name($rs,$i)."><option value=''></option>";for($j=0;$j<count($listenum);$j++){echo "<option value='".$listenum[$j]."'>".$listenum[$j]."</option>";}echo "</select>";
									}
								
							}
							else{
									echo "<input type=text name=".mysql_field_name($rs,$i)." size=".mysql_field_len($rs,$i).">";
							}
						break;
					case "date":
						echo "<input type='text' class='span2' name='".mysql_field_name($rs,$i)."' value='".date('d-m-Y')."' id='".mysql_field_name($rs,$i)."' >
						<script>
							$(function(){
								$('#".mysql_field_name($rs,$i)."').datepicker({
									format: 'dd-mm-yyyy'
								});
							});
						</script>";
						break;
					case "blob":
						if(strpos(mysql_field_flags($rs,$i),"binary")){
						//GAMBAR
							echo "<input type=file name=".mysql_field_name($rs,$i).">";
						}
						else{
						//TEXT
							echo "<textarea cols=50 rows=5 name=".mysql_field_name($rs,$i)."></textarea>";
						}
					break;
					default:
							echo "<input type=text name=".mysql_field_name($rs,$i)." size=".mysql_field_len($rs,$i).">";
						break;
					}
				//echo "</td></tr>";
				  ///////////////////////////////////////////////////////////
				  echo "</p>";
			    }

			//echo "</table>";
			echo "<input type=submit value=simpan onclick=";?>"return confirm('Benarkah hendak menyimpan?')"<?php echo"></fieldset></form>";
			
		break;
		//fungsi simpan 
		case "save":
			$sql="INSERT into $table VALUES('";
			$rs=mysql_query("select * from $table");
			for($i=0;$i<mysql_num_fields($rs);$i++){
				if(mysql_field_type($rs,$i)=="blob"&&strpos(mysql_field_flags($rs,$i),"binary")){
					if($_FILES[mysql_field_name($rs,$i)]['name']){
					$tmp_s=$_FILES[mysql_field_name($rs,$i)]['tmp_name'];
					$tmp_d=date('Ymdhis').".".substr($_FILES[mysql_field_name($rs,$i)]['type'],-3);
					move_uploaded_file($tmp_s, $tmp_d);
					$fp=fopen($tmp_d, 'r');
					$file_content=fread($fp, filesize($tmp_d));
					$field_value[]=mysql_real_escape_string($file_content);
					fclose($fp);
					unlink($tmp_d);
					}else{
					$field_value[]="";
					}
				}
				elseif(mysql_field_type($rs,$i)=="date"){
					$field_value[]=$_REQUEST['yyyy']."-".$_REQUEST['mm']."-".$_REQUEST['dd'];										
				}
				else{
					$field_value[]=$_REQUEST[mysql_field_name($rs,$i)];					
				}
			}
		    $sql=$sql.implode("','",$field_value)."')";
			mysql_query($sql);
			//echo $sql;
			echo "<script language=javascript>self.location='$filename?table=$table&mode=view'</script>";

		break;
		case "edit":
		$rs=mysql_query("select * from $table where id_$table='".$_REQUEST['id_'.$table]."'");
		while($row=mysql_fetch_array($rs)){
		echo "<form action='$filename?table=$table&mode=update' class='form-horizontal'method=post enctype='multipart/form-data'>";
		//echo "<table border=1>";
		echo "<fieldset>";
				for($i=0;$i<mysql_num_fields($rs);$i++){
				echo "<p><label><b>".str_replace("_"," ",mysql_field_name($rs,$i))."</b></label><br>";
/*				  echo "<tr><td>".str_replace("_"," ",mysql_field_name($rs,$i))."</td><td><input type=text name='".mysql_field_name($rs,$i)."' value='".$row[mysql_field_name($rs,$i)]."'></td></tr>"; */
///////////////////////////////
					switch(mysql_field_type($rs,$i)){
					case "int":
					    if(mysql_field_len($rs,$i)==11){
							//INT SMALL
							if($i!=0 && substr(mysql_field_name($rs,$i),0,3)=='id_'){
								echo "<select name='".mysql_field_name($rs,$i)."'>";
								$rs2=mysql_query("select * from ".substr(mysql_field_name($rs,$i),3));
								while($row2=mysql_fetch_array($rs2)){
									echo "<option value='".$row2[0]."'";if($row[mysql_field_name($rs,$i)]==$row2[0]){echo " selected";}echo">".$row2[1]."</option>";
								}
								echo"</select>";
							}
							else{
								echo "<input type=text name=".mysql_field_name($rs,$i)." size=11 value='".$row[mysql_field_name($rs,$i)]."'>";
							}
						}
						elseif(mysql_field_len($rs,$i)==20){
							//INT BIG
							echo "<input type=text name=".mysql_field_name($rs,$i)." size=20 value='".$row[mysql_field_name($rs,$i)]."'>";
						}
												else{
							//INT BIG
							echo "<input type=text name=".mysql_field_name($rs,$i)." size=3 value='".$row[mysql_field_name($rs,$i)]."'>";
						}
						break;
					case "string":
					    //STRING
							if(mysql_field_flags($rs,$i)=="enum"){
								$rsenum=mysql_query("SELECT column_type FROM information_schema.columns WHERE table_name = '$table' AND column_name = '".mysql_field_name($rs,$i)."'");
								$rowenum=mysql_fetch_array($rsenum);$listenum=substr($rowenum['column_type'],6);$listenum=substr($listenum,0,-2);$listenum=explode("','",$listenum);
								if(count($listenum)<=2){
									echo "<input type=radio name='".mysql_field_name($rs,$i)."' value='".$listenum[0]."'";if($row[mysql_field_name($rs,$i)]==$listenum[0]){echo " checked=checked";}echo "> ".$listenum[0]."   <input type=radio name='".mysql_field_name($rs,$i)."' value='".$listenum[1]."'";if($row[mysql_field_name($rs,$i)]==$listenum[1]){echo " checked=checked";}echo "> ".$listenum[1];	
								}
								else{
									echo "<select name=".mysql_field_name($rs,$i)."><option value=''></option>";for($j=0;$j<count($listenum);$j++){echo "<option value='".$listenum[$j]."'";if($row[mysql_field_name($rs,$i)]==$listenum[$j]){echo " selected";}echo ">".$listenum[$j]."</option>";}echo "</select>";
									}
								
							}
							else{
								echo "<input type=text name=".mysql_field_name($rs,$i)." size=".mysql_field_len($rs,$i)." value='".$row[mysql_field_name($rs,$i)]."'>";								
							}
						break;
					case "date":
						//TANGGAL
						echo "<input type='text' class='span2' name='".mysql_field_name($rs,$i)."' value='".date('d-m-Y',strtotime($row[mysql_field_name($rs,$i)]))."' id='".mysql_field_name($rs,$i)."' >
						<script>
							$(function(){
								$('#".mysql_field_name($rs,$i)."').datepicker({
									format: 'dd-mm-yyyy'
								});
							});
						</script>";
						break;
					case "blob":
						if(strpos(mysql_field_flags($rs,$i),"binary")){
						//GAMBAR
							echo "<img src='data:image/jpg;base64,".base64_encode($row[mysql_field_name($rs,$i)]). "' width=100><br><input type=file name=".mysql_field_name($rs,$i).">";
						}
						else{
						//TEXT
							echo "<textarea cols=50 rows=5 name=".mysql_field_name($rs,$i).">".$row[mysql_field_name($rs,$i)]."</textarea>";
						}
					break;
					default:
							echo "<input type=text name=".mysql_field_name($rs,$i)." size=".mysql_field_len($rs,$i)." value='".$row[mysql_field_name($rs,$i)]."'>";
						break;
					}
					echo "</p>";
///////////////////////////////
			    }
		//echo "</table>";
		echo "<input type=submit value=update onclick=";?>"return confirm('Benarkah hendak menyimpan?')"<?php echo">
		</fieldset>
		</form>";
		
		}
		echo "</div>";
		break;
		case "update":
		///////////////////
			$sql="UPDATE $table SET ";
			$rs=mysql_query("select * from $table where id_$table='".$_REQUEST["id_".$table]."'");
			for($i=0;$i<mysql_num_fields($rs);$i++){
				if(mysql_field_type($rs,$i)=="blob" && strpos(mysql_field_flags($rs,$i),"binary")){
					if($_FILES[mysql_field_name($rs,$i)]['name']){
					$tmp_s=$_FILES[mysql_field_name($rs,$i)]['tmp_name'];
					$tmp_d=date('Ymdhis').".".substr($_FILES[mysql_field_name($rs,$i)]['type'],-3);
					move_uploaded_file($tmp_s, $tmp_d);
					$fp=fopen($tmp_d, 'r');
					$file_content=fread($fp, filesize($tmp_d));
					$field_value[]=mysql_field_name($rs,$i)."='".mysql_real_escape_string($file_content)."'";
					fclose($fp);
					unlink($tmp_d);
					}else{
						while($row=mysql_fetch_array($rs)){$field_value[]=mysql_field_name($rs,$i)."='".mysql_real_escape_string($row[mysql_field_name($rs,$i)])."'";}
					}
				}
				elseif(mysql_field_type($rs,$i)=="date"){
					$field_value[]=mysql_field_name($rs,$i)."='".date('Y-m-d',strtotime($_REQUEST[mysql_field_name($rs,$i)]))."'";										
				}
				else{
					$field_value[]=mysql_field_name($rs,$i)."='".$_REQUEST[mysql_field_name($rs,$i)]."'";					
				}
			}
		    $sql=$sql.implode(",",$field_value)." WHERE id_$table='".$_REQUEST["id_".$table]."'";
			mysql_query($sql);
			echo "<script language=javascript>self.location='$filename?table=$table&mode=view'</script>";

		///////////////////
		break;
		case "delete":
		mysql_query("delete from $table where id_$table='".$_REQUEST['id_'.$table]."'");
		echo "<script language=javascript>self.location='$filename?table=$table&mode=view'</script>";
		break;		
		case "laporan":
					$rs=mysql_query("select * from $table");
			echo "<h2>Laporan $table</h2>";
			echo "<table border=1><tr>";
			for($i=0;$i<mysql_num_fields($rs);$i++){
				echo "<th>".mysql_field_name($rs,$i)."</th>";
			}
			echo "</tr>";
			while($row=mysql_fetch_array($rs)){
				echo "<tr>";
				for($i=0;$i<mysql_num_fields($rs);$i++){
				echo "<td>";
				/*echo "<td>".$row[mysql_field_name($rs,$i)]."</td>";*/
				/////////////////////////
									switch(mysql_field_type($rs,$i)){
					case "int":
					    if(mysql_field_len($rs,$i)==11){
							//INT SMALL
								if($i!=0 && substr(mysql_field_name($rs,$i),0,3)=='id_'){
								$rs2=mysql_query("select * from ".substr(mysql_field_name($rs,$i),3)." where ".mysql_field_name($rs,$i)."='".$row[mysql_field_name($rs,$i)]."'");
								while($row2=mysql_fetch_array($rs2)){
									echo $row2[1];
								}
							}
							else{
								echo $row[mysql_field_name($rs,$i)];
							}

							
						}
						elseif(mysql_field_len($rs,$i)==20){
							//INT BIG
							echo $row[mysql_field_name($rs,$i)];
						}
						else{
							//INT BIG
							echo $row[mysql_field_name($rs,$i)];
						}
						break;
					case "string":
					    //STRING
					    echo $row[mysql_field_name($rs,$i)];
						break;
					case "date":
						//TANGGAL
						if($row[mysql_field_name($rs,$i)]==null){
							echo "";
						}
						else{
							echo date('d-m-Y',strtotime($row[mysql_field_name($rs,$i)]));
						}
						break;
					case "blob":
						if(strpos(mysql_field_flags($rs,$i),"binary")){
						//GAMBAR
					    echo "<img src='data:image/jpg;base64,".base64_encode($row[mysql_field_name($rs,$i)]). "' class='img-responsive img-thumbnail' width=48>";
						}
						else{
						//TEXT
							echo $row[mysql_field_name($rs,$i)];
						}
					break;
					default:
						echo $row[mysql_field_name($rs,$i)];
						break;
					}
                  echo "</td>";
				/////////////////////////
			    }
				echo "</tr>";
			}
			echo "</table>";

		break;
		case "login":
		
		echo "
		<div class='art-block clearfix' style='width:60%;margin:auto;'>
        <div class='art-blockheader'>
            <h3 class='t'>Login</h3>
        </div>
        <div class='art-blockcontent'>

		<form action='$filename?mode=ceklogin' method=post>
		  <fieldset class='input' style='border: 0 none;'>
    <p id='form-login-username'>
      <label for='modlgn_username'>Username</label>

      <input id='modlgn_username' type='text' name='username' class='inputbox' alt='username' style='width:100%' />
    </p>
    <p id='form-login-password'>
      <label for='modlgn_passwd'>Password</label>
      <input id='modlgn_passwd' type='password' name='password' class='inputbox' size='18' alt='password' style='width:100%' />
    </p>
    <p id='form-login-level'>
      <label for='modlgn_level'>Level</label>";
	  $listlevel=array();
	  for($i=0;$i<count(default_level());$i++){
	     $listlevel[]="<option value='".default_level()[$i]."'>".default_level()[$i]."</option>";
	  }
	  echo "
      <select name=level style='width:100%'><option></option>".implode(" ",$listlevel)."</select>
    </p>

<input type=submit value=Login class=art-button>  

  </fieldset>
		</form>
		
		
		
</div>
</div>
		";
		break;
		case "ceklogin":
			if(mysql_num_rows(mysql_query("select * from $_REQUEST[level] where username='$_REQUEST[username]' and password='$_REQUEST[password]'"))>0){
			$rs=mysql_query("select * from $_REQUEST[level] where username='$_REQUEST[username]' and password='$_REQUEST[password]'");
			while($row=mysql_fetch_array($rs)){
			   $level=$_REQUEST['level'];
			   $_SESSION['level']=$level;
			   $_SESSION['id_'.$level]=$row[0];
			   $_SESSION['nama']=$row[1];
			}
			echo "<script language=javascript>alert('Login Sukses, Selamat Datang $_SESSION[nama]!');self.location='index.php?mode=home'</script>";
			
			}else{
			echo "Login Gagal";
			}
		break;
		case "logout":
			session_destroy();
			echo "<script language=javascript>alert('Logout Sukses, $_SESSION[nama]!');self.location='index.php?mode=home'</script>";
		break;
		case "profil":
			   if(!file_exists('profil.txt')){file_put_contents("profil.txt","Ini Adalah Profil");}
			   if(!isset($_REQUEST['profil'])){
			        if(isset($_SESSION['level'])){
					echo "
					<form action='index.php?mode=profil' method=post>
					<textarea name=profil>".file_get_contents("profil.txt")."</textarea><br>
					<input type=submit value=simpan>
					</form>
					";}
					else{
					   echo file_get_contents("profil.txt");
					}
			   }
			   else{
			       file_put_contents("profil.txt",$_REQUEST['profil']);
			echo "<script language=javascript>alert('Perubahan Berhasil disimpan, $_SESSION[nama]!');self.location='index.php?mode=profil'</script>";
			   }
			break;
		case "home":
		       if(!file_exists('home.txt')){file_put_contents("home.txt","Selamat Datang");}
			   if(!isset($_REQUEST['home'])){
			        if(isset($_SESSION['level'])){
					echo "
					<form action='index.php?mode=home' method=post>
					<textarea name=home>".file_get_contents("home.txt")."</textarea><br>
					<input type=submit value=simpan>
					</form>
					";}
					else{
					   echo file_get_contents("home.txt");
					}
			   }
			   else{
			       file_put_contents("home.txt",$_REQUEST['home']);
			echo "<script language=javascript>alert('Perubahan Berhasil disimpan, $_SESSION[nama]!');self.location='index.php?mode=home'</script>";
			   }
			break;
	}
}
//2. Menu FUNCTION
function menu(){
	global $db;
	global $filename;
	if(!isset($_REQUEST['export'])){
echo "
<nav class='art-nav'>
    <div class='art-nav-inner'>
    <ul class='art-hmenu'>";
	echo "<li><a href='index.php?mode=home'>Home</a></li>";
		echo "<li><a href='index.php?mode=profil'>Profil</a></li>";
		if(isset($_SESSION['level'])){
	echo "<li><a href='#'>Master</a><ul>";
			$rst=mysql_query("show tables from $db");
		while($rowt=mysql_fetch_array($rst))
		{
	
			echo "<li><a href='".$filename."?table=".$rowt[0]."'>".ucwords(str_replace("_"," ",$rowt[0]))."</a></li>";
		}	
echo "</ul></li>";
echo "<li><a href='#'>Laporan</a><ul>";
			$rst=mysql_query("show tables from $db");
		while($rowt=mysql_fetch_array($rst))
		{
	
			echo "<li><a href='".$filename."?table=".$rowt[0]."&mode=laporan'>".ucwords(str_replace("_"," ",$rowt[0]))."</a></li>";
		}	
echo "</ul></li>";
}
            if(!isset($_SESSION['level'])){
			echo "<li><a href='index.php?mode=login'>Login</a></li>";
			}
			else{
			echo "<li><a href='index.php?mode=logout'>Logout</a></li>";
			}
			
			
echo "
	</ul> 
	
        </div>
    </nav>
";	}
}
?>
