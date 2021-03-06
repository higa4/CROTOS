<?php
/* / */
/* Search for subs P279 and part of P361 for indexes */
echo "\nSubs and parts of";
include $file_timer_begin;

$link = mysqli_connect ($host,$user,$pass,$db) or die ('Erreur : '.mysqli_error());
mysqli_query($link,"TRUNCATE `prop_sub`");
$tab_props=array(31,135,136,144,170,180,186,195,276,608,921,941);
//$tab_props=array(31,135,136,144,180,186,195,276,921,941);
for ($i=0;$i<count($tab_props);$i++){
	$prop=$tab_props[$i];
	echo "\ntable p".$prop;
	$sql="SELECT id, qwd, P18 from p$prop";
	$rep=mysqli_query($link,$sql);

	while($data = mysqli_fetch_assoc($rep)) {
		$id_prop=$data['id'];
		$qwd=$data['qwd'];
		$sub_query="";
        if (($qwd!=0)&&($qwd!=15989253)){
            if (($prop!=170)&&($prop!=179)&&($prop!=608)){
                $res = get_query($prop,$qwd);
                if (count($res)>0){
                    for ($j=0;$j<count($res);$j++){
                        if ($res[$j]!="683074"){
                        $sql="SELECT id from p$prop WHERE qwd=".$res[$j];
                        $rep2=mysqli_query($link,$sql);
                        if (mysqli_num_rows($rep2)>0){
                            $row = mysqli_fetch_assoc($rep2);
                            $id_sub=$row['id'];	
                            $rep3=mysqli_query($link,"INSERT INTO prop_sub (prop,id_prop,id_sub) VALUES (".$prop.",".$id_prop.",".$id_sub.") ");
                            $sub_query.=" OR id_prop=".$id_sub;
                        }}
                    }
                }
            }
            //Pour chaque on fait recherche de 279 ou 461 selon la propriété
            //on le lie à la propriété (test si trop long on ne lie pas)
            $sql="SELECT count(distinct id_artw) as total from artw_prop  WHERE prop=$prop and (id_prop=".$id_prop.$sub_query.")";
            $rep2=mysqli_query($link,$sql);
            $data2=mysqli_fetch_assoc($rep2);
            $nbartworks=$data2['total'];
            
            $sql="SELECT count(distinct artworks.id) as total from artworks, artw_prop  WHERE artworks.id=artw_prop.id_artw and  artworks.P18!=0 and artw_prop.prop=$prop and (id_prop=".$id_prop.$sub_query.")";
            $rep2=mysqli_query($link,$sql);
            $data2=mysqli_fetch_assoc($rep2);
            $nbimg=$data2['total'];
            
            $sql="UPDATE p$prop SET nb=".$nbartworks.", nbimg=".$nbimg." WHERE id=".$id_prop;
            mysqli_query($link,$sql);
        }
	}
}
mysqli_close($link);

echo "\nSubs and parts of done";
include $file_timer_end;
?>