<?php
//Abstract class declaration begins here for all the actions on the site

//Set up the action classes


//Abstract class for all actions on the site begins here ----------------------------------------------------------------------
abstract class Action implements JsonSerializable {
	/*
	Generic class for some action on the website
	 */
	abstract public function log_action();
	
	protected function toArray() {
		/*
		(Null) -> Array
		Ouputs the Array for JSON encoding
		*/
		$jsonArray = array();
		foreach ($this as $attribute=>$value) {
			$jsonArray[$attribute] = $value;
		}
		$jsonArray['class'] = $this->get_class();
		return $jsonArray;
	}

	public function get_class() {
		/*
		(Null) -> String
		Gets the class for the current object for the JSON encoding
		*/
		return get_class($this);
	}

	public function jsonSerialize() {
		/*
		(Null) -> Null
		Magic Method for JSON serializing
		*/
		return $this->toArray();
	}
}


abstract class MessageAction extends Action {
	/*
	Parent class for some action done on the messaging system of the site
	 */
	
}


abstract class SiteAction extends Action {
	/*
	Class for some action on the site
	*/
}


abstract class ThreadAction extends Action {
	/*
	Abstract class for some action on a thread
	 */
}


abstract class BadmintonDateAction extends Action {
	/*
	Abstract class for some badminton date action
	 */
}
?>