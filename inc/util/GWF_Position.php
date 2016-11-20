<?php
final class GWF_Position
{
	private $lat;
	private $lng;
	
	public function __construct($lat=null, $lng=null)
	{
		$this->lat = $lat;
		$this->lng = $lng;
	}
	
	public function lat() { return $this->lat; }
	public function lng() { return $this->lng; }
	
	public function toJSON() { return sprintf('{lat:%.08f,lng:%.08f}', $this->lat, $this->lng); }
	
	################
	### Distance ###
	################
	public function distanceTo(GWF_Position $pos) { return $this->distanceToLatLng($pos->lat(), $pos->lng()); }
	public function distanceToLatLng($lat, $lng) { return GWF_Geolocate::distanceCalculation($this->lat, $this->lng, $lat, $lng); }
	
}