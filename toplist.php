<?
require("include/top.php");

if ($platform == 'any platform')
	unset($platform);
if ($type == 'prods')
	unset($type);
	
if (!$dayspans) $dayspans=30;
if (!$dayspane) $dayspane=0;
if (!$prodlimit) $prodlimit=10;

$prodlimit = min($prodlimit,1000);

// get all the platforms
$query="select * from platforms order by name asc";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
}

// get all the types
unset($tmp);
unset($types);
$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
$tmp = explode("'",$row[1]);
for($i=1;$i<count($tmp);$i+=2)
  $types[]=$tmp[$i];

// get the top 10 prods
  unset($tmp);
  $query ="SELECT prods.id,prods.name,prods.group1,prods.group2,prods.group3,prods.type ";
  $query.="FROM prods ";
  if ($platform) $query.=", prods_platforms, platforms ";
  $query.="WHERE quand>DATE_SUB(sysdate(),INTERVAL '$dayspans' DAY) AND quand<DATE_SUB(sysdate(),INTERVAL '$dayspane' DAY) ";
  if ($platform)
	$query.="and prods_platforms.platform=platforms.id and platforms.name='".$platform."' and prods_platforms.prod=prods.id ";
  if ($type)
	$query.="AND FIND_IN_SET('".$type."',prods.type)>0 ";
  $query.="ORDER BY (prods.views/((sysdate()-prods.quand)/100000)+prods.views)*prods.voteavg*prods.voteup desc LIMIT $prodlimit";
  $result = mysql_query($query);
  while($tmp = mysql_fetch_assoc($result)) {
    $top10[] = $tmp;
  }
  for($j=0;$j<count($top10);$j++) {
    $query="SELECT name FROM groups WHERE id=".$top10[$j]["group1"];
    $result=mysql_query($query);
    if(mysql_num_rows($result))
      $top10[$j]["groupname1"]=mysql_result($result,0);
    $query="SELECT name FROM groups WHERE id=".$top10[$j]["group2"];
    $result=mysql_query($query);
    if(mysql_num_rows($result))
      $top10[$j]["groupname2"]=mysql_result($result,0);
    $query="SELECT name FROM groups WHERE id=".$top10[$j]["group3"];
    $result=mysql_query($query);
    if(mysql_num_rows($result))
      $top10[$j]["groupname3"]=mysql_result($result,0);
  }
?>
<br />
<form action="<?=basename($SCRIPT_FILENAME)?>" method="get">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" width="100%">
    <tr>
     <td bgcolor="#224488">
		<table width="100%"><tr>
	     <td><b>prodtype</b></td>
		 <td>
			<select name="type">
			<option>prods</option>
			<? foreach ($types as $t) { ?>
			<? if ($type == $t) : ?>
			<option selected><?=$t?></option>
			<? else : ?>
			<option><?=$t?></option>
			<? endif; ?>
			<? } ?>
			</select>
		 </td>
	     <td><b>platform</b></td>
		 <td colspan="2">
			<select name="platform">
			<option>any platform</option>
			<? foreach ($platforms as $p) { ?>
			<? if ($platform == $p["name"]) : ?>
			<option selected><?=$p["name"]?></option>
			<? else : ?>
			<option><?=$p["name"]?></option>
			<? endif; ?>
			<? } ?>
			</select>
		 </td>
		 <td ><input type="image" src="gfx/submit.gif"></td>
	  </tr>
	  <tr>
	  <td><b>prodlimit</b></td>
	  <td><input type="text" name="prodlimit" size="4" value="<? print($prodlimit); ?>"><br /></td>
	  <td><b>backdays</b></td>
	  <td align="left"><input type="text" name="dayspans" size="10" value="<? print($dayspans); ?>"><br /></td>
	  <td align="left"><input type="text" name="dayspane" size="10" value="<? print($dayspane); ?>"><br /></td>
	  </tr>
	  </table>
	 </td>
    </tr>
    <? for($j=0;$j<count($top10);$j++): ?>
     <tr>
      <td bgcolor="#446688">
       <?=$j+1?>.
       <b><a href="prod.php?which=<?=$top10[$j]["id"]?>"><?=$top10[$j]["name"]?></a></b>
       <? if(strlen($top10[$j]["groupname1"])>0): ?>
        by <a href="groups.php?which=<? print($top10[$j]["group1"]); ?>"><? print(strtolower($top10[$j]["groupname1"])); ?></a>
       <? endif; ?>
       <? if(strlen($top10[$j]["groupname2"])>0): ?> &amp; <a href="groups.php?which=<? print($top10[$j]["group2"]); ?>"><? print(strtolower($top10[$j]["groupname2"])); ?></a>
       <? endif; ?>
       <? if(strlen($top10[$j]["groupname3"])>0): ?> &amp; <a href="groups.php?which=<? print($top10[$j]["group3"]); ?>"><? print(strtolower($top10[$j]["groupname3"])); ?></a>
       <? endif; ?>
      </td>
     </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>
</form>
<br />

<? require("include/bottom.php"); ?>
