<?php
/*
   This view implements Google Map

$this->api->addLocation(
    'atk4-addons/misc/templates/js','js')
    ->setParent($this->api->pathfinder->base_location);
$p->js()->_load('univ.google.map');

$map=$p->add('View_Google_Map');
$map->renderMap(53.35,-6.26);
$map->width=390; $map->height=300;


// additional features

 $map->bindLatLngZoom($lat, $lng, $zoom);
 $map->bindLocationFields($country, $city, $address);

 $map->showMapForEdit();

 $map->bindRefreshAfterChange($country);
 $map->bindRefreshAfterChange($city);
 $map->bindRefreshAfterChange($address);

TODO: need samples


   */
class View_Google_Map extends HtmlElement {
	public $width=640;
	public $height=480;
	function init(){
		parent::init();

		$this->set('Problem Loading Google Map');

		$url='http://maps.google.com/maps?file=api&v=2&key='.
			$this->api->getConfig('map/google/key','ABQIAAAA1dw0K5D0tpWLBbZ-SCh2YBS8lnDegradZ_LGHIBXZSRvdsbN5BQJtPnF1vFmNBWgvU-R-TiiESQV2g');

		$this->api->template->append('js_include',
			'<script type="text/javascript" src="'.$url.'"></script>'."\n");
	}
	function setWidthHeight(){
		$this->addStyle(array('width'=>$this->width.'px','height'=>$this->height.'px'));
	}
	function render(){
		$this->setWidthHeight();
		parent::render();
	}
	function showMapForEdit(){
		$this->js(true)->univ()->showMapForEdit();
	}
	function renderMap($latitude,$longitude,$zoom=null){
		$this->js(true)->univ()->renderMap($latitude,$longitude,$zoom);
	}
	function getMarkerForLocation($country, $city, $addess){
		$this->js(true)->univ()->getMarkerForLocation($country,$city,$addess);
	}
	function bindLatLngZoom($lat, $lng,$zoom=null){
		$this->js(true)->univ()->bindLatLngZoom($lat, $lng, $zoom);
	}
	function bindLocationFields($country, $city, $addess){
		$this->js(true)->univ()->bindLocationFields($country, $city, $addess);
	}
	function bindRefreshAfterChange($name){
		if (is_array($name)){
			foreach ($name as $_name){
				$this->js(true)->univ()->bindRefreshAfterChange($_name);
			}
		}
		else {
			$this->js(true)->univ()->bindRefreshAfterChange($name);
		}
	}
}
