<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.4.0
*/namespace
Adminer;const
VERSION="5.4.0";error_reporting(24575);set_error_handler(function($Cc,$Ec){return!!preg_match('~^Undefined (array key|offset|index)~',$Ec);},E_WARNING|E_NOTICE);$ad=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($ad||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$tj=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($tj)$$X=$tj;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Fb=adminer()->credentials();$J=Driver::connect($Fb[0],$Fb[1],$Fb[2]);return(is_object($J)?$J:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$Ie=substr($u,-1);return
str_replace($Ie.$Ie,$Ie,substr($u,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($va,$x,$k=null){return($va&&array_key_exists($x,$va)?$va[$x]:$k);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$ah,$ad=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($x,$X)=each($ah)){foreach($X
as$Ae=>$W){unset($ah[$x][$Ae]);if(is_array($W)){$ah[$x][stripslashes($Ae)]=$W;$ah[]=&$ah[$x][stripslashes($Ae)];}else$ah[$x][stripslashes($Ae)]=($ad?$W:stripslashes($W));}}}}function
bracket_escape($u,$Ca=false){static$cj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($u,($Ca?array_flip($cj):$cj));}function
min_version($Jj,$We="",$g=null){$g=connection($g);$Vh=$g->server_info;if($We&&preg_match('~([\d.]+)-MariaDB~',$Vh,$A)){$Vh=$A[1];$Jj=$We;}return$Jj&&version_compare($Vh,$Jj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_bool($ke){$X=ini_get($ke);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Ij,$N,$V,$F){$_SESSION["pwds"][$Ij][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$m=0,$tb=null){$tb=connection($tb);$I=$tb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$m]:false);}function
get_vals($H,$d=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$d];}return$J;}function
get_key_vals($H,$g=null,$Yh=true){$g=connection($g);$J=array();$I=$g->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Yh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$g=null,$l="<p class='error'>"){$tb=connection($g);$J=array();$I=$tb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.error()."\n";return$J;}function
unique_array($K,array$w){foreach($w
as$v){if(preg_match("~PRIMARY|UNIQUE~",$v["type"])){$J=array();foreach($v["columns"]as$x){if(!isset($K[$x]))continue
2;$J[$x]=$K[$x];}return$J;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($x);}function
where(array$Z,array$n=array()){$J=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$d=escape_key($x);$m=idx($n,$x,array());$Xc=$m["type"];$J[]=$d.(JUSH=="sql"&&$Xc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^json~',$Xc)?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Xc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($m,q($X))))));if(JUSH=="sql"&&preg_match('~char|text~',$Xc)&&preg_match("~[^ -@]~",$X))$J[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$J[]=escape_key($x)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$n=array()){parse_str($X,$Wa);remove_slashes(array(&$Wa));return
where($Wa,$n);}function
where_link($s,$d,$Y,$Xf="="){return"&where%5B$s%5D%5Bcol%5D=".urlencode($d)."&where%5B$s%5D%5Bop%5D=".urlencode(($Y!==null?$Xf:"IS NULL"))."&where%5B$s%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$n,array$M=array()){$J="";foreach($e
as$x=>$X){if($M&&!in_array(idf_escape($x),$M))continue;$wa=convert_field($n[$x]);if($wa)$J
.=", $wa AS ".idf_escape($x);}return$J;}function
cookie($B,$Y,$Pe=2592000){header("Set-Cookie: $B=".urlencode($Y).($Pe?"; expires=".gmdate("D, d M Y H:i:s",time()+$Pe)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($Bb){parse_str($_COOKIE[$Bb],$Zh);return$Zh;}function
get_setting($x,$Bb="adminer_settings",$k=null){return
idx(get_settings($Bb),$x,$k);}function
save_settings(array$Zh,$Bb="adminer_settings"){$Y=http_build_query($Zh+get_settings($Bb));cookie($Bb,$Y);$_COOKIE[$Bb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($id=false){$Aj=ini_bool("session.use_cookies");if(!$Aj||$id){session_write_close();if($Aj&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Ij,$N,$V,$j=null){$xj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($j!==null?"db|":"").($Ij=='mssql'||$Ij=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$xj,$A);return"$A[1]?".(sid()?SID."&":"").($Ij!="server"||$N!=""?urlencode($Ij)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($j!=""?"&db=".urlencode($j):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Se,$lf=null){if($lf!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Se!==null?$Se:$_SERVER["REQUEST_URI"]))][]=$lf;}if($Se!==null){if($Se=="")$Se=".";header("Location: $Se");exit;}}function
query_redirect($H,$Se,$lf,$jh=true,$Jc=true,$Sc=false,$Pi=""){if($Jc){$oi=microtime(true);$Sc=!connection()->query($H);$Pi=format_time($oi);}$ii=($H?adminer()->messageQuery($H,$Pi,$Sc):"");if($Sc){adminer()->error
.=error().$ii.script("messagesPrint();")."<br>";return
false;}if($jh)redirect($Se,$lf.$ii);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$T,$Fc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Fc($R)))return
false;}return
true;}function
queries_redirect($Se,$lf,$jh){$eh=implode("\n",Queries::$queries);$Pi=format_time(Queries::$start);return
query_redirect($eh,$Se,$lf,$jh,false,!$jh,$Pi);}function
format_time($oi){return
sprintf('%.3f s',max(0,microtime(true)-$oi));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($ug=""){return
substr(preg_replace("~(?<=[?&])($ug".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($x,$Rb=false,$Xb=""){$Zc=$_FILES[$x];if(!$Zc)return
null;foreach($Zc
as$x=>$X)$Zc[$x]=(array)$X;$J='';foreach($Zc["error"]as$x=>$l){if($l)return$l;$B=$Zc["name"][$x];$Xi=$Zc["tmp_name"][$x];$yb=file_get_contents($Rb&&preg_match('~\.gz$~',$B)?"compress.zlib://$Xi":$Xi);if($Rb){$oi=substr($yb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$oi))$yb=iconv("utf-16","utf-8",$yb);elseif($oi=="\xEF\xBB\xBF")$yb=substr($yb,3);}$J
.=$yb;if($Xb)$J
.=(preg_match("($Xb\\s*\$)",$yb)?"":$Xb)."\n\n";}return$J;}function
upload_error($l){$gf=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'Unable to upload a file.'.($gf?" ".sprintf('Maximum allowed file size is %sB.',$gf):""):'File does not exist.');}function
repeat_pattern($Gg,$y){return
str_repeat("$Gg{0,65535}",$y/65535)."$Gg{0,".($y%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Tc=false){$J=table_status($R,$Tc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$p){foreach($p["source"]as$X)$J[$X][]=$p;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$B=bracket_escape($x,true);$J[$B]=array("field"=>$B,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($x==driver()->primary),);}return$J;}function
dump_headers($Qd,$wf=false){$J=adminer()->dumpHeaders($Qd,$wf);$qg=$_POST["output"];if($qg!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Qd).".$J".($qg!="file"&&preg_match('~^[0-9a-z]+$~',$qg)?".$qg":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$x=>$X){if(preg_match('~["\n,;\t]|^0|\.\d*0$~',$X)||$X==="")$K[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($r,$d){return($r?($r=="unixepoch"?"DATETIME($d, '$r')":($r=="count distinct"?"COUNT(DISTINCT ":strtoupper("$r("))."$d)"):$d);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$o=@tempnam("","");if(!$o)return'';$J=dirname($o);unlink($o);}}return$J;}function
file_open_lock($o){if(is_link($o))return;$q=@fopen($o,"c+");if(!$q)return;@chmod($o,0660);if(!flock($q,LOCK_EX)){fclose($q);return;}return$q;}function
file_write_unlock($q,$Lb){rewind($q);fwrite($q,$Lb);ftruncate($q,strlen($Lb));file_unlock($q);}function
file_unlock($q){flock($q,LOCK_UN);fclose($q);}function
first(array$va){return
reset($va);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$q=file_open_lock($o);if(!$q)return'';$J=stream_get_contents($q);if(!$J){$J=rand_string();file_write_unlock($q,$J);}else
file_unlock($q);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$_,array$m,$Oi){if(is_array($X)){$J="";foreach($X
as$Ae=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($Ae):"")."<td>".select_value($W,$_,$m,$Oi);return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$m);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$J=adminer()->editVal($X,$m);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Oi!=""&&is_shortable($m))$J=shorten_utf8($J,max(0,+$Oi));else$J=h($J);}return
adminer()->selectVal($J,$_,$m,$X);}function
is_blob(array$m){return
preg_match('~blob|bytea|raw|file~',$m["type"])&&!in_array($m["type"],idx(driver()->structuredTypes(),'User types',array()));}function
is_mail($tc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$gc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Gg="$xa+(\\.$xa+)*@($gc?\\.)+$gc";return
is_string($tc)&&preg_match("(^$Gg(,\\s*$Gg)*\$)i",$tc);}function
is_url($Q){$gc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($gc?\\.)+$gc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$m){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea|hstore~',$m["type"]);}function
host_port($N){return(preg_match('~^(\[(.+)]|([^:]+)):([^:]+)$~',$N,$A)?array($A[2].$A[3],$A[4]):array($N,''));}function
count_rows($R,array$Z,$ue,array$wd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($ue&&(JUSH=="sql"||count($wd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$wd).")$H":"SELECT COUNT(*)".($ue?" FROM (SELECT 1$H GROUP BY ".implode(", ",$wd).") x":$H));}function
slow_query($H){$j=adminer()->database();$Qi=adminer()->queryTimeout();$di=driver()->slowQuery($H,$Qi);$g=null;if(!$di&&support("kill")){$g=connect();if($g&&($j==""||$g->select_db($j))){$De=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$De&token=".get_token()."'); }, 1000 * $Qi);");}}ob_flush();flush();$J=@get_key_vals(($di?:$H),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$hh=rand(1,1e6);return($hh^$_SESSION["token"]).":$hh";}function
verify_token(){list($Yi,$hh)=explode(":",$_POST["token"]);return($hh^$_SESSION["token"])==$Yi;}function
lzw_decompress($Ia){$cc=256;$Ja=8;$gb=array();$uh=0;$vh=0;for($s=0;$s<strlen($Ia);$s++){$uh=($uh<<8)+ord($Ia[$s]);$vh+=8;if($vh>=$Ja){$vh-=$Ja;$gb[]=$uh>>$vh;$uh&=(1<<$vh)-1;$cc++;if($cc>>$Ja)$Ja++;}}$bc=range("\0","\xFF");$J="";$Sj="";foreach($gb
as$s=>$fb){$sc=$bc[$fb];if(!isset($sc))$sc=$Sj.$Sj[0];$J
.=$sc;if($s)$bc[]=$Sj.$sc[0];$Sj=$sc;}return$J;}function
script($fi,$bj="\n"){return"<script".nonce().">$fi</script>$bj";}function
script_src($yj,$Ub=false){return"<script src='".h($yj)."'".nonce().($Ub?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$Za,$Fe="",$Wf="",$db="",$He=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($Za?" checked":"").($He?" aria-labelledby='$He'":"").">".($Wf?script("qsl('input').onclick = function () { $Wf };",""):"");return($Fe!=""||$db?"<label".($db?" class='$db'":"").">$J".h($Fe)."</label>":$J);}function
optionlist($bg,$Nh=null,$Bj=false){$J="";foreach($bg
as$Ae=>$W){$cg=array($Ae=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($Ae).'">';$cg=$W;}foreach($cg
as$x=>$X)$J
.='<option'.($Bj||is_string($x)?' value="'.h($x).'"':'').($Nh!==null&&($Bj||is_string($x)?(string)$x:$X)===$Nh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$bg,$Y="",$Vf="",$He=""){static$Fe=0;$Ge="";if(!$He&&substr($bg[""],0,1)=="("){$Fe++;$He="label-$Fe";$Ge="<option value='' id='$He'>".h($bg[""]);unset($bg[""]);}return"<select name='".h($B)."'".($He?" aria-labelledby='$He'":"").">".$Ge.optionlist($bg,$Y)."</select>".($Vf?script("qsl('select').onchange = function () { $Vf };",""):"");}function
html_radios($B,array$bg,$Y="",$Rh=""){$J="";foreach($bg
as$x=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>$Rh";return$J;}function
confirm($lf="",$Oh="qsl('input')"){return
script("$Oh.onclick = () => confirm('".($lf?js_escape($lf):'Are you sure?')."');","");}function
print_fieldset($t,$Ne,$Mj=false){echo"<fieldset><legend>","<a href='#fieldset-$t'>$Ne</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$t');",""),"</legend>","<div id='fieldset-$t'".($Mj?"":" class='hidden'").">\n";}function
bold($La,$db=""){return($La?" class='active $db'":($db?" class='$db'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($D,$Ib){return" ".($D==$Ib?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>");}function
hidden_fields(array$ah,array$Ud=array(),$Sg=''){$J=false;foreach($ah
as$x=>$X){if(!in_array($x,$Ud)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($Sg?$Sg."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
enum_input($U,$ya,array$m,$Y,$wc=""){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$Ze);$Sg=($m["type"]=="enum"?"val-":"");$Za=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($m["null"]&&$Sg?"<label><input type='$U'$ya value='null'".($Za?" checked":"")."><i>$wc</i></label>":"");foreach($Ze[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Za=(is_array($Y)?in_array($Sg.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$ya value='".h($Sg.$X)."'".($Za?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$J;}function
input(array$m,$Y,$r,$Ba=false){$B=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$th=(JUSH=="mssql"&&$m["auto_increment"]);if($th&&!$_POST["save"])$r=null;$rd=(isset($_GET["select"])||$th?array("orig"=>'original'):array())+adminer()->editFunctions($m);$Bc=driver()->enumLength($m);if($Bc){$m["type"]="enum";$m["length"]=$Bc;}$dc=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$B]".($m["type"]=="enum"||$m["type"]=="set"?"[]":"")."'$dc".($Ba?" autofocus":"");echo
driver()->unconvertFunction($m)." ";$R=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($rd[""])."<td>".adminer()->editInput($R,$m,$ya,$Y);else{$Dd=(in_array($r,$rd)||isset($rd[$r]));echo(count($rd)>1?"<select name='function[$B]'$dc>".optionlist($rd,$r===null||$Dd?$r:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($rd))).'<td>';$me=adminer()->editInput($R,$m,$ya,$Y);if($me!="")echo$me;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$ya value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ya value='1'>";elseif($m["type"]=="set")echo
enum_input("checkbox",$ya,$m,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($m)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($r=="json"||preg_match('~^jsonb?$~',$m["type"]))echo"<textarea$ya cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($Mi=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$Y)){if($Mi&&JUSH!="sqlite")$ya
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$ya
.=" cols='30' rows='$L'";}echo"<textarea$ya>".h($Y).'</textarea>';}else{$nj=driver()->types();$if=(!preg_match('~int~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$A)?((preg_match("~binary~",$m["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$m["unsigned"]?1:0)):($nj[$m["type"]]?$nj[$m["type"]]+($m["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$m["type"]))$if+=7;echo"<input".((!$Dd||$r==="")&&preg_match('~(?<!o)int(?!er)~',$m["type"])&&!preg_match('~\[\]~',$m["full_type"])?" type='number'":"")." value='".h($Y)."'".($if?" data-maxlength='$if'":"").(preg_match('~char|binary~',$m["type"])&&$if>20?" size='".($if>99?60:40)."'":"")."$ya>";}echo
adminer()->editHint($R,$m,$Y);$bd=0;foreach($rd
as$x=>$X){if($x===""||!$X)break;$bd++;}if($bd&&count($rd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $bd);");}}function
process_input(array$m){if(stripos($m["default"],"GENERATED ALWAYS AS ")===0)return;$u=bracket_escape($m["field"]);$r=idx($_POST["function"],$u);$Y=idx($_POST["fields"],$u);if($m["type"]=="enum"||driver()->enumLength($m)){$Y=$Y[0];if($Y=="orig")return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($m["auto_increment"]&&$Y=="")return
null;if($r=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($r=="NULL")return"NULL";if($m["type"]=="set")$Y=implode(",",(array)$Y);if($r=="json"){$r="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(is_blob($m)&&ini_bool("file_uploads")){$Zc=get_file("fields-$u");if(!is_string($Zc))return
false;return
driver()->quoteBinary($Zc);}return
adminer()->processInput($m,$Y,$r);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Qh="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Wg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Qh<li>".($I?$Wg:"<p class='error'>$Wg: ".error())."\n";$Qh="";}}}echo($Qh?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($mb,$bi=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $mb, $bi) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$K,$wj,$l=''){$_i=adminer()->tableName(table_status1($R,true));page_header(($wj?'Edit':'Insert'),$l,array("select"=>array($R,$_i)),$_i);adminer()->editRowPrint($R,$n,$K,$wj);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($n
as$B=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$qh))$k=$qh[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$wj&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$B,""):($wj&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$wj&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($wj?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($wj?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."…', this); };"):"");}echo($wj?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$ui=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$A);return
h($A[1]).$ui.(isset($A[2])?"":"<i>…</i>");}function
icon($Pd,$B,$Od,$Si){return"<button type='submit' name='$B' title='".h($Si)."' class='icon icon-$Pd'><span>$Od</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ħ̐±ܔ͈\"PѩҭcQCa¤鉲ó鈞d<̦󡼤:;NBqR;1Lf³9Ȟu7&)¤l;3͑񈀊/CQXʲ2MƑa䩰)°쥺LuÝh歹Ս23lȎi7³mڷ4њ<-Ҍ´¹!U,Févt2S,¬䡴҇FꖘúaNq㩓-֎ǜh꺮5û9ș¨;jµ-޷_9krùٓ;.КtTq˯¦0³­ֲ®{�ùý\r爮싍GS Zh²;¼i^ÀuxøWΒC@Ķ¤©kҽ¡Т©ˢ켯Aؠ0¤+(ڐÁ°l\ꠃx躜r耢8\0新!\0FƜnB͎㨒3 \r\\º۪Ȅa¼'I⼪(i\n\r©¸ú4Oüg@4ÁCº@@!đB°݉°¸c¤ʂ¯ı,\r1Eh舐&2PZ¦ퟒ�GûH9G\"v§ꌒ¢££¤4rƱ͏DВ¤\npJ뭁|/.¯c꓄u·£¤ö:,ʽ°¢RŝU5¥mVÁk͌LQ@-\\ª¦˓@9Á㜥ړrÁαMPDらa\r(YY\\〘õpê:£p÷lLC ű踎͊O,\rƲ]7?m06仰ܖTш͡ҥC;_˗ѹȴd>¨²bnퟖ�n¼ܣ3÷X¾ö8\r훋-)۩>V[Y㹦L3¯#̎X|ՉX \\ù`ˈC§瘥#љHɌ2ʲ.# öZ`¼¾㳮·¹ªÒ£º\0uh־¥M²͟\niZeO/CӒ_`3ݲ𱃾=Ы3£R/;䯤ۜ\0ú㞚µmùú򾤷/«֕AΘ¿°ñ.½sጣý :\$Ɇ¢¸ª¾£w8󟾾«HԪ­\"¨¼¹Գ7gSõ䱇∆L鎯瑲_¤O'Wض]c=ý5¾1X~7;iþ´\r�n¨JS1Z¦ø£؆ߓͣ吂tüAԖ퐸6fФù;Y]©õzIÀp¡ѻ§𣉳®Y˝}@¡\$.+1¶'>Zãpd੒GL桄#k��zY҄Auϕvݎ]s9ђ؟AqΌÁ:ƅ\nKhB¼;­֚XbAHq,❃Iɠ窹S[ˌ¶1ƖӔr񔻶pނÛ)#鐉;4̈񒯪ռ³L Á;lfª\n¶s\$K`нƴՔ£¾7jx`d%j] ¸4Y¤HbY ؊`¤GG .ŜK򦌊I©)2MfָݘRC¸̱V,©ۑ~g\0薂৶ݺõ[j�½:AlIq©u3\"ꦁq¤漸<9s'㑝JʼМ0`p ³jfOƢЉú¬¨q¬¢\$邩²ñJ¹>RH(ǔq\n#re(y󖇊µ0¡Q҈£򈶆P曃:·G伞 ݴ©ҞӰÐZµ\\´訜n֩~¦´°9R%דj·{7䰞_ǳ	z|8ňꙉ\"@ܣ9DVLŜ$H5ԘWJ@z®a¿J Ğ	)®2\nQvÀԝ뇎āj (A¸Ӛ°BB05´6b˰][諪Awvkg􆴶ºիk[jmzc¶}荹DZi휤5e«ʷ°º	A CY%.Wb*뮼.­ٳq/%}B̘­皖337ʻaº򞷗[጗Qʝ޲ü_Ȕ2`ǱIѩ,÷曣Mf&(s-䘫AİتDw؄TNÀɻŪX\$鸪+;аˆڕ93µJkS;·§ÁqR{>l;B1AȐI♢) (6±­r÷\rݜrڇڟ욛R^SOy/ލ#Ə9{kસv\"úKC⊃¨rEo\0ø̜\,љ|fa͚³hI©/o̙4ċk^pHȞͰhǡVÁvox@ø`�&(ù­ü;~Ǎz̶׸¯*°Ɯ5®ܝ±E Á°颮Ә¤´3öņgrDь󩴧{»佥³©L&ú>脻¢ؚZ췡\0ú°̊@אӛffŌRVh֝²盉ۈ½ⰲӷ) =x^,k2��jࢫl0u랜"¬fp¨¸1񒉿z[]¤wpN6dIªz뵿宮7X{;Áȳ؋-I	⻼7pjÝ¢R#ª,ù_-м󾳀\\檛WqޱJ֘uh£ІbLÁKԥ繖ľ©¦Þѕ®µªüV{K}S ʝޝMþ·̀¼¦.M¶\\ªix¸bÁ¡1+£α?<ų꾈ýӜ$÷\\вۜ$ضtԏ̈㜤s¼¼©xľx󧈃ᮓkVĉ=z6½¡ʧæ䎡¢ָhܼ¸º±ý¯R¤噣8g¢䊷:_³�ҒIRKÝ¨.½nkVU+dwj§%³`#,{醳˗ퟩ�Yý׵(oվɰ.¨c0g℘Ok7®苤Όlҍhx;Ϝ؏ ݃Lû´\$09*9 ܨNrüMՂ.>\0زP9ȧ	\0\$\\F󪲤'εL庋bú𖴏2À􅢰9Àퟀ�nb쭎¤󅠣ĜɃ 겐Yꨠt͠؅\n𵂮©ʅ⮜$op lX\n@`\r	ȜrЈ Έ ¦  	 ʆઆ𚈠Ή@ڋ@ڜn  	\0j@Q@1\rÀ@ ¢	\$p	 V\0򅠠\n\0¨\n Мn@¨' 쌀¤\n\0`\rÀڈ ¬	Ҝrऌ ´\0Џr°挀򉜰`	இ {	,\"¨ȞP0¥\n¬4±\n0·¤.0Ìpˌ𓜲pۜr𣎰뎰󏰻񙐱񙑑0ߒ%ђѱQ8\n ԏ\0􈫊ȼ\0^ҏ\0`چ@´ȏ>\nѯ1w±,Y	h*=¡P¦:іV¸.q£Ō͜r՜rp鎐񐱁сQ	ёї1ג `ѝ񯓱7±랱򜲠^À䏜"y`\nÀ # \0ꉠp\n򜮀` r Q𦢧1ҳ\n°¯#°µ#𼌱¥\$q«\$ѱ%0奱½%й&Ǧq͑ &񛧱ڜrR}16	 b\r`µ`ܜrÀ	ވÀ̌dઆ¨	j\n¯``À\n`dcсP,򱙒י\$¿rIҏ 	Q	򙏳2b1ɦϰ1ӑљ ӏ fÀϓ\0ª\0¤ Άf\0j\n f`≠®\n`´@\$n=`\0ȎҶ nIМ$ÿP(¤'˰􄌿Ġ·gɶ--҃7R皠 	4ࠅ��˦±ѝ2t\r��n 	H*@	`\n ¤ 艠򆬕2¿,z\r쾈 脜rF촨ö؄ 뭐õ䄬´z~¡\0]Ğ\\¥׉\\¥£}ItC\nÁT}ªؗIEJ\rx׉û¾ٍpIH��fht믮bxYE쩝K´ªoj\n𭅌Àއtr׮À~d»H2U4©GܜAꂧ4þuPtރՖ½谐 򐠍L/¿P׉\"G!RtO-̎µ<#õAPuI뒨\$c¹ÄƊ §¢-Çⴏ`Pv§^W@tH;Q°µRę՜$´©gK膼\rR*\$4®' 󍨐Ȋ[�Iª󎭕mсƨ:+þ¼5@/­l¾I¾ª�^\0ODøª¬؜rR'r蔐­[ꖷĄª®«MC덃Z4慠B\"栶´euN�¬靏𴺜rª`܀hö*\r¶.V%ڡMBlPFϜ"د&կ@\Cޯ©:mMgn򎮶ʩ8I2\rp��÷﫚 mT©ueõզv>f´И֠DU[ZTϖЃേT𜲖¹Uvkõ^ז¦øL딙b/¾K¶Sev2÷ubvǏVD𖉭՜$򥖘?ud硗|,\rø+nUeךƄʖþö뭾X¯ºûԶBGd¶\$i¶獶!t#L쳯·UIOu?ZweRϘ 룷ª. `ȡiø񜲢§%©b∅¦H®\"\"\"h�\$b@Ẫ䆜0f\"鲗¨®*悋|\$\$¬B֗ \"@r¯(\r`ʏ ¸ǂ(0&.`Ҏk9B\n&#(Ī℀䂯څ«dü^÷º®ü £@²`ҌI-{0£✮B{4sG{§ø;z®©b÷{ ѻbׯ){BxKÀŇ5=cڪ«y宦슣Prŉ/ܠ\0ڋ▜r¥׉퉈=¸£N\\ئ=Ë菽XV파x¹µإˋx²©døՊی*H'¦δ¸»{Xƽ؊=\0¼\0¾¹囉«JڴٹOإ¹؉螜røý ʄXý§ŇĽ}׺°¾ ù)y'٧Ñى̨ù[l(5`f\\Á`¿ùe.lY(¹=zה!Y%h¾O¹+ù`ٙ\"e 拧ėºK򹥾¿¯£¸ÿ ߚ٣S¹EIYû.H֊tG·`¾H¼J5»͵~ ¸6C¥hø§ùXDz\nx¡yshFK¡c¡zj¢ZY8(¹þ%ِ|yI«£ߑ؃چ饡úY¡X»¡u¢ڠ´ک]¦ڣ¡ڍ¥ú;ȧù򾇡Q T©øüú¨ [~W龙c݂z©úµz¥º½¢ú\r¬:  \0貙û¢x)ʡªúɡ¹K¦ú+§z!£ӀC+°´ٮ⃯:ݎ§ª¤ú©¢Zgû~z4f¥¯	¥:÷£sºӪ꫚õxʂ%»=³Gۉf3?ú㎸¿µ+Y´úq¶@̻Gúᙹ¶»oµّ´۰\rª~Á{W¶[·¹鮹躜0Ɯ\»·;e¹ۡ¶YI\"·¸zdk©Zö|[uuτ+׹9q¼¹nR ˮ¥B»ؗz|\rᤄýk¤^»[1ªۥ.pA­2<۽¼ء蜤黖5)³m¸!»јXýºYø¨5vT\\®QÀ%:À¢>Àɛۻ¸e|/·yÁń§ŗ§xנ|g®ӄC݆\\ü¼<¼9z\\®#𮆖;8¡莍X7ø׊Μ"8&d5¬P4Gj?ʜ0ܿ\"=­ùHER");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ħƃȨ0ÁLЁह1¢S!¤ۉF!°朢-6NĢdGgӈ°º;Nr£)öc7\r稈آ81s9¼¤ܫ\r磩ʭ8OVA¡£1c34Of*ª- P¨1©r41ٮ6̤2ց®ۯ½܌#3BǦ#	֧9Φꘌfc\rǆIЂb6EC&¬ЬbuĪm7aV㕂Ás²#m!��岹޶\\3\rL:SA¤k5ݮǟ·׬ýʒaF¸3阒e6fS¦빾󈸲!ǌú -΋,̳L‘ºJ¶˲¢*J 䬵£¤»	¸𓗂¹Áb©c蠹­깹¤怏Ԑ迃Hܸ£ \\·È궾«`𖅎¸޻A༄T'¨p&q´qE괅\rl­è¼5#pώȒ ѣIݥꦗBI؞ܲ¨>ʫ29<«嗃2¯¶7j¬8jҬc(nԄ翨a\0ŏ@5*3:δ涌£氌㭂́ÀlLPƴ@ʉ°Ꜥ¡H¥4 n31¶汍t򰮡͙9闏!¨r¼ڔ؜ە興£ùQ°¹6膱¬«<ø7°\r-xC\n ܣ®@Ҹܔԑ:\$iܘ¶m«ª˴틩d¬²{\n6\rxhˋ⣞'4Vø@a͇<´#h0¦S歅c¸ֹ+p«a2ԣyh®BO\$Á繶wiXɔùVY9*r÷Htm	@b֑|@ü/l\$z¦­ +ԥp2lɄ.õغՖ۬ķﻘǦ{À˭X¨C<l9𭶸9ﭬ򙤃¯À­7RüÀ0\\괎÷Pș)AȯÀxĚq͏#¸¥Ȧ[;»ª6~Pۜra¸ʚTGT0謗u¸ޟ¾³ޜn3𜜠\\ʎJ©udªCGÀ§©PZ÷>³Áûd8֒¨話½􃿖·dLퟔ�.(ti­>«,􃃖Ò+9iޞC\$䝘#\"΁ChVb\nЊ6𔲃ewᜮf¡À6m	!1'cÁ他تeLRn\r쾇\$􃲓\$ᘰÀꙡ'«l6&ø~Ad\$느\$s ¦ȃB4򉝩jª.ÁRC̔Qj\"7\n㘳!²6=΂Ȁ}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':̢Фi1㳱ԝ	4̀£̑6a&󐰇:OAI줥:NFᄼݡCyꭲ˅\"㉔ʲ<̱ي/C#ٶ:DbqSeJ˦Cܺ\n\n¡ǱS\rZH\$RAܞS+XKvtdܧ:£�EvXŞ³jɭҩejײM§©亁B«ǈ&ʮL§C°3呰ՌƩ-x蜮ә섓ȂyNa䐮:煛¼䨳͐( cLŜ/õ£(ƈ5{޴Qy4øg-ý¢ꩴڃfЎ(իbUýϫ·܎&㺎ä��b¾¢ؠ.­ہ\rΐܼ»σĺ¼͜n ©ChҼ\r)`蘥`淥CʒȢZùµ㘊<QűX÷¼@·0dp9EQüf¾°ӆ؜r䡍拨h��Ünp'#Č¤£H̔(i*r¸榼#¢淋Ȉ~# ȓA:N6㰊©lլ§\r􁊐γ£!@Ҳ>Cr¾¡¬h°N᝞¦(a0M3Ͳ׶ԕ愣E2'!<·£3R<𛂏㘒攃Hη#n䫱a\$!蜲Ј0¤.°wd¡r:Yö¨酲慡]<¹j⥳@ߜ\װl§_\rÁZ¸ғ¬TͩZɳ򎳏\"²~9À©³jㅉPؖ)QYbݕD동c¿`zᣞµѨ̛'룴BOh¢*2ÿ<ŒOꦧ-Z£գ 踎aОú+r2bø\\ᾰ©ᾌ¥ùש¸Áޮٞp!#`卫Zö¸6¶12׃@鲫yȆ9\r줂3烰ޅ輣!p9஑o6s¿𣆘3홠bA¨ʶ񹦽ÀZ£#6ûʥ?s¨Ȝ"ω|؂§)þbJc\r»½Nޟsɛih8ϐ¹束躞;躈垌õuI5û@豍ªA萗aH^\$H׶ㅖ@ÛL~¨ùb9'§ø¿±S?PЭ¯򘰍C𜮘R򎭌4ޓȓ:Àõ܇Ը򄌴µh(k\njIȶ\"EY#¹Wrª\rG8£@tСXԓ⌂S\nc0ɫC I\rʰ<u`A!󩎐Բփ¢\0=¾ 斡䐈1ӢK!¹!埐pĝIsѬ6⤞éɎi1+°ȏ┫꼕¸^	ᜮɲ0´Fԉ_\$멦\0 ¤C8E^¬į3W!א)u*䟑Ԩ&\$ꔲY\n©]Ek񄚖¨\$xTse!RY» R`=L򸣄ޫ\nl_.!²V!r\nHЫ²\$א`{1	|± °i<jRrPTG|w©4b´\r¡ǆ4d¤,§E¡ȶ©䏼è[Nq@Oi׾'ѩ\r¥󗻦]#潐0»ASIJdс/QÁ´⸵t\r¥UGğG<鍼y-Iɺ򄤝М" P B\0ý텀ȞÁq`Aa̡J堒䊮)JB.¦TܱL¡÷ Cpp\0(7cYYa¨M鱕em4Ӗc¢¸r£«S)o񍠂p权!I¼¾Sb0m챎(dEHø¸߳Xª£/¬P©踙yƘ鸵Ȓ\$+֖»²gd茀öΎyݐ܏³Jט렢lE¢ur̬dCX}e¬셑¥õ«m]в ̽Ȩ-z¦Z庻Iö\) ,\n¤>򩷞¤朲VS\njx*w`ⴷSFi̓d¯¼,»ᐖZFM}Њ À\\Z¾P읠¹zؚûE]�ɟO룭ԁ]À ¬Á%þ\"w4¥\n\$øɺV¢SQDۺݶ«䇋wMԮS0B-sƪ)㾚�|˞R8kMsd¹ka)h%\"PͰnn÷/Á#;֧\rdȐ¸8ކ<3\$©,咐);<4`Λ¢<2\nʵ钇@w-®ፗAϜ0¹ºª¹Lr옙Cࡏ>º昴ºLõ첂yto;2ݑª±trm躧A�¡÷ANºݜ\\"kº5oV뉃=7r1ݰ䁶\\+9ª※°瞨if¬=·rҏºuڊût؝yӞЙùCö¶ºÁ³ҵ݇ܧi¥vf݂ù+¥Ø|ʬ;¸ ]~ӊ|\re÷¥쿓݂څ'�¦䔯²°	½\0+Wcoµw6wd Su¼j¨3@򰡣÷\n .wm[8x<²ˣM¬\n9ý²ý'aùގ1>Ȅ£[¶ﵺdx¯༜"Yc¸ނ!i¹¥ꕷÀ}��¹kººܘ]­¶¸ԒÀ{󉗚R¥=f W~杉(bea®'ubףּ>)\$°P÷᭚6þR*IGu#ƕUKµAXtѨӠ_ܢ ¾£p¸ &Uˋى퉝ýÁYG6P]Ar!b¡ *ЙJoµӯ哿󁯁򶽽*À ء难_ªÀٴB³_~RBiKùþ`牦Jە\0­􌮎\0М$̅þ僂K SЎ򢪚¤І ̻0pvMJ bN`Lÿ歃eº/`RO.0P串`ꉥüƂ¸d GxǢP-(@ɸӀ洨H%<&À̚قÀ腰¬°%\0®pЇЄø꣉¯	ȯ\"ö¢J³¢\ns_À̜rৎ`!k䰘	萺Ķ�p\$ú'ퟜ�RUeZÿ¨d\$윮LᎆBº↳.ޤnҴm>vj䕭)	Mº\r\0®ʊHќ"5*!eºZJº蒫ㆦ(dc±¼(xܑjg\0\\õµÀ¶ Z@º઼`^r)<(ȩ̜몳ʐ쀙k­̭l3Qyс@ɘѐfάPn璼¨Д 򯍎·mRձ³�mvúN֍|úШZ²ȆڨYpø\"4Ǩ栲&lҐ`Ā£Xx bbdв0Fr5°<»C挲z¨¯6䨥!¤\rdz؋;Ĵ³²\nٍ HƋQ\$QEnn¢n\rÀ©#T\$°²ˈ(ȟѩ|c¤,¼-ú#蚜r ܡJµ{dѝE\n\$²ƂriTԲ+ŲPEDBe}&%Rf²¥\nü^􈃒ȚڠRVŁ,ѻ«缎윰O1锪c^\r%\r 쫠Ү\0y1蔮°\r´ĂK1捳H®\r\"û0\0NkXPr¸¯{3 콉\nSȤڗx.Z񒔱wS;53 .¢s4sO3FºٲS~YFpZs¡'΀ّOqR4\n­6q6@Dhٶ͕7vE¢l\"Ş;-娂&Ϣ*²*򮡠䜲!#縧G\"͆wÁ\"úՠȏ2!\"R(vÀX漜"D̶À¦)@ᆓ,¸zm򁆍wT@ÀԠ Мn֓𺐫hдIDԐ\$m>朲&`>´4ȒA#*룒<w\$T{\$´4@dӴRem6¯-#Dd¾%E¥DT\\ \$)@܋´WC¬(t®\"Mܣ@úTF\r,g¦\rP8þ´֣Jü°c öĹƂꠊ\"LªZԤ\r+P4ý=¥¤S♔õA)0\"¦CDhǍ\n%F԰֓ü|fLNlFtDmH¯ªþ°5彈͜nļ4ü³õ\$ྋ񶜲bZਜr\"pEQ%¤wJ´ÿV0ԅM%嬜"hPFၣ®򯇒6 h6]5¥\$fS÷CLiRT?R¨þC񵣈U§Z¤晢Fþ/殪Zܜ"\"^ι´6RG ²̮⺜\$ªѥ\\&O֨v^ ϋUºѮΒam³(\rﺌ¯¾ü\$_ª楱+KTtض.ٖ36\n룵:´@6 újPÁQõF/S®k\"<4AgAСU\$'놈ӡf໑O\"׫~²S;ŀ½󮯋: k¼9­ü²󄎥]`nú¼ҭ7¨;V˝⸗À©2H¢U®YlB�ö⯎֔´°¶ö	§ý⮰®։l¾m\0񴂲)¥XÁ\0ʂQ߱FSq4ÿnFx+pԲ¦EƓovúGW7oׄw׋RW׈\r4`|cq,ױ9·u ϵ÷cq䒜"LC tÀh⩧\rʏÀ\\øW@ɧ|D#S\r%5l桥++垇k^ʙ`/7¸(z*񘋀ퟝ�Eݻ¦S(W׭Xė0V£0ˑ¥	~릂닕2Q­ꂲu mC¬됄£tr(\0Q!K;xNýWÀúÿ§øȁ?b< @ŗ`֘,º`0eºƂN'²¤&~øtӵ\"| ¬i 񂥠 7¾Rø ¸lSu°8AûdF%(Ժ 亯󿳀A-oQź@|~©KÀʞ@x󟍢~D¦@س¸TNŚC	W҂ix<\0P|Ħ\n\0\n`¨¥ ¹\"&?st|ïwਭd굀N£^8À[t©9ªB\$అ§©ퟖ�'\">U~ÿ98 铲ÔFĦ °¹uȅ°/)9À\0ᘫAùz\"FWAx¤\$'©jG´(\"ٌ ±s%THe,	M7¼ ǅء ˓ƃ·&wYԏ3°ظ /\rϖù¯ٻ\"ùݜp{%4b󌠭¤Ե~n卅3	Ό °9峘ֿd䕏Zŏ9栗@¨l»f¯õؑbP¤*Go兠8¨¯ùA悼Àz	@¦	ݒb¡Zn_ͨº'ѢF\$f¬§`ö󺆈dDdH%4\rs΁jLRȧ޹fڹg IϘ,R\\·øʖ>\nH[´\"°À\rӁL̬%놌l8gzL缰ko\$ǫ­᠒ËPԶ値ϧV:V؍ü%±蕀ø6Ǽ\r๔«®LE´NԀS#ö.¶[x4¾a猭´LL® ª\n@£\0۫tٲ圮^F­º¥º5`͝ R7ȬL uµ(dº¡¹ Ԝr䂦/uCf״ÿcҞ B﬎_´nLԜ0© \$»Ʀ¶¸~ÀUkﶥe􋥦˲\0ZaZXأ¦|Cq¨/<}س¡Ńº²º¶ Zº*­w\nO㇅z`¼5®18¶cøû®¯­®暉ÀQ2YsǜK朮£\\\"­ ðc򖪵B¶钱<3+õņµ*ؓ雵4ӭ쭛:RhITdevΉµH䨒-Zw\\ƥn赶\n̗ө\$Յow¬+© ºù˲ɂ¶&Jq+û}҄฼Ӫ«dŎ?敥BBeǯM¶Nm=τ󕷂¢\$HRfªwb|²x dû2掩S೘gɀ߾Γv §|﫲x½\0{ԃR=Fÿώ΢®ϣr½8	ퟡ�vȸ*ʳ£{2Sݫ;S¦ӨƫyL\$\"_۫©B縇¬ݜ"E¸%ºŚº\nøЂp¾p''«p󷘕Ҫ\"8бI\\ @ ʾ Lnퟺ�RߣM䞄µþqLNƮ\n\\̎\$`~@`\0u牾^@լ-{5񔬀bruÁo[Á²¾¨ս鯱y.ש {鶱°RpМ$¸+13ۚúڐú+¨O!D)® ܮu<¯,«ᱟ=Jdƫ}µd#©0ɞcӂ3U3»EY¹û¢\rû¦tj5ҥ7»e©wׄǡúµ¢^q߂¿9Ɵ<\$}k퍲RI-ø°¸+'_Ne?SیR�*X4鮼c}¬蜢@vi>;5>Dn \r䫩bN镵P@Y䇼񖨶iõ#PB2A½-흰d0+ퟗ�Kûø¿�n飼ddøOÀ¯塆cüi<ú0\0\\ù끑gù檡NTi'  ·��mjᐜŷ»¸uΊ+ªV~À²ù 'ol`ù³¿󜢬ü̚£דFÀ喉ý⻃©¸¤þT aώEۃQư´ p+?ø\nƾ'l½¤* tɆKάp°(YC\n-q̔0圢*ɕÁ,#üⷷº\"%¨+qĎ¸ꂱ°=婮@x7:ťGcYIН0*kÀۈ\\·¯𑐟{¤ ŇǣÁý\r终³[p¨ >7ӣh뮍΂Ԯµ£¦S|&J򍇾8´ÀmOhþĭ	ՑqJ&aݢ¨'.b珰ج\$ö­܌D@°CHB	Ȧ❡|\$Ԭ-6°²+̫  pºଡAC\rɓ쯎0´񐂮¢MéZnE͢j*>û!Ңu%¤©gذ£
