<?php
// Run with wp eval-file on a disposable WordPress install, active PixTypes.
wp_set_current_user(1);
register_post_type('pile_portfolio', array('public'=>true,'supports'=>array('title','revisions')));
require_once WP_PLUGIN_DIR.'/pixtypes/features/metaboxes/init.php';
$r=new ReflectionClass('cmb_Meta_Box');$box=$r->newInstanceWithoutConstructor();
$p=$r->getProperty('_meta_box');$p->setAccessible(true);$p->setValue($box,array('pages'=>array('pile_portfolio'),'fields'=>array(array('id'=>'_pile_project_builder','type'=>'pix_builder'),array('id'=>'ordinary_text','type'=>'text'))));
$id=wp_insert_post(array('post_type'=>'pile_portfolio','post_title'=>'Round trip regression','post_status'=>'draft'));
$values=array(
 'quotes'=>wp_json_encode(array(array('type'=>'text','content'=>'<p class="intro">A "quote", O\'Brien and C:\\work</p>'))),
 'unicode'=>wp_json_encode(array(array('type'=>'text','content'=>'Iași — café, 雪'))),
);
$failed=0;
foreach($values as $label=>$json){
 $_POST=array('wp_meta_box_nonce'=>wp_create_nonce('pixtypes_save_metabox'),'post_type'=>'pile_portfolio','_pile_project_builder'=>wp_slash($json),'ordinary_text'=>wp_slash("O'Brien"));
 $box->save($id);$box->save($id);$saved=get_post_meta($id,'_pile_project_builder',true);
 $ok=$saved===$json && json_decode($saved,true)!==null;
 echo ($ok?'PASS':'FAIL')." save $label\n";if(!$ok)$failed++;
 update_post_meta($id,'_pile_project_builder',wp_slash($json));
 $revision=wp_insert_post(array('post_type'=>'revision','post_parent'=>$id,'post_title'=>'Test revision','post_status'=>'inherit'));
 pixbuilder_save_revision_meta($revision,get_post($revision));
 $ok=get_metadata('post',$revision,'_pile_project_builder',true)===$json;
 echo ($ok?'PASS':'FAIL')." revision $label\n";if(!$ok)$failed++;
 pixbuilder_on_restore_revision($id,$revision);
 $ok=get_post_meta($id,'_pile_project_builder',true)===$json;
 echo ($ok?'PASS':'FAIL')." restore $label\n";if(!$ok)$failed++;
 wp_delete_post($revision,true);
}
$_POST=array('wp_meta_box_nonce'=>'invalid','post_type'=>'pile_portfolio','_pile_project_builder'=>'broken');
$before=get_post_meta($id,'_pile_project_builder',true);$box->save($id);
$ok=$before===get_post_meta($id,'_pile_project_builder',true);echo ($ok?'PASS':'FAIL')." invalid nonce preserves data\n";if(!$ok)$failed++;
wp_delete_post($id,true);
if($failed)WP_CLI::error("$failed builder round-trip assertions failed");
