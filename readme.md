PHP Library for Cloud API of SAKURA Internet
============================================

This is the library for [Cloud API of SAKURA Internet](http://developer.sakura.ad.jp/cloud/api/).

Requirements
------------

* PHP 5.2 or later with the following extensions:
    * cURL with SSL(HTTPS)
    * JSON

Usage
-----

    require 'src/sacloud.php';

    $sacloud = new Sacloud('Your Access Token', 'Your Access Token Secret');

    // Get Server Status
    $status = $sacloud->server('Your Server ID')->getStatus();
    
    or

    $status = $sacloud->api('/server/{Your Server ID}', 'GET');

If you use the configuration file "src/config.inc.php" (renamed from "config.inc.php.sample"), constructor arguments are omissible.

    $sacloud = new Sacloud();

License
-------

Copyright 2011 Masashi Sekine <sekine@cloudrop.jp>.

Licensed under the Apache License, Version 2.0 (the "License"); you may
not use this file except in compliance with the License. You may obtain
a copy of the License at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
License for the specific language governing permissions and limitations
under the License.
