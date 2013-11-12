<?php

/************************* LdapHelper for PHP **********************************
Copyright (c) 2012, University of Trier - ZIMK, Matthias Lohr
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met: 

1. Redistributions of source code must retain the above copyright notice, this
list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies, 
either expressed or implied, of the FreeBSD Project.
 *******************************************************************************/

namespace Daps\LdapBundle\Security\Ldap;

use \stdClass;

/**
 * Class LdapEnvironment
 *
 * @author Matthias Lohr <matthias@lohr.me>
 */
class LdapEnvironment {

	private $zone;
	private $serverList;

	public function __construct($zone) {
		$this->zone = $zone;
		$this->refresh();
	}

	private static function compareDnsRecordPriority($recordA, $recordB) {
		return ($recordA['pri'] - $recordB['pri']);
	}

	protected static function discoverLdapServers($zone) {
		$result = array();
		// search for LDAPS servers
		$ldapServers = dns_get_record('_ldaps._tcp.'.$zone, DNS_SRV);
		uksort($ldapServers, array('LdapEnvironment','compareDnsRecordPriority'));
		foreach ($ldapServers as $server) {
			$tmp = new stdClass();
			$tmp->hostname = $server['target'];
			$tmp->port = $server['port'];
			$tmp->isSslPort = true;
			$result[] = $tmp;
		}
		// search for non-SSL LDAP servers
		$ldapServers = dns_get_record('_ldap._tcp.'.$zone, DNS_SRV);
		uksort($ldapServers, array('LdapEnvironment','compareDnsRecordPriority'));
		foreach ($ldapServers as $server) {
			$tmp = new stdClass();
			$tmp->hostname = $server['target'];
			$tmp->port = $server['port'];
			$tmp->isSslPort = false;
			$result[] = $tmp;
		}
		return $result;
	}

	public function getServerList() {
		return $this->serverList;
	}

	public function getZone() {
		return $this->zone;
	}

	public function refresh() {
		$this->serverList = self::discoverLdapServers($this->zone);
	}
}
