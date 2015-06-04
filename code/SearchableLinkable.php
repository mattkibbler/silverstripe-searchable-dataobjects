<?php

/**
 * SearchableLinkable - interface to allow DataOBject to be searchable and reachable with a link
 *
 * @author Firebrand <developers@firebrand.nz>
 * @creation-date 04-June-2015
 */
interface SearchableLinkable extends Searchable {
	
	/**
	 * Link to access this DO
	 * @return string
	 */
	public function Link();
	
}
