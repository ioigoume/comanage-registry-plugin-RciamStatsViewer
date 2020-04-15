<?php
/**
 * COmanage Registry Rciam Stats Viewer Plugin Language File
 *
 * Portions licensed to the University Corporation for Advanced Internet
 * Development, Inc. ("UCAID") under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.
 *
 * UCAID licenses this file to you under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with the
 * License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v2.0.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
  
global $cm_lang, $cm_texts;

// When localizing, the number in format specifications (eg: %1$s) indicates the argument
// position as passed to _txt.  This can be used to process the arguments in
// a different order than they were passed.

$cm_rciam_stats_viewer_texts['en_US'] = array(
  // Titles, per-controller
  'ct.rciam_stats_viewers.1'          => 'Statistics Viewer',
  'ct.rciam_stats_viewers.pl'         => 'Statistics Viewers',
  'ct.rciam_stats_viewer_services.pl' => 'Statistics Viewer',
  
  // Plugin texts
  'pl.rciamstatsviewer.hostname'        => 'Hostname',
  'pl.rciamstatsviewer.hostname.desc'   => 'Hostname of the external database you want to access',
  'pl.rciamstatsviewer.stats_type'      => 'Type of Statistics',
  'pl.rciamstatsviewer.stats_type.desc' => 'Select the type of statistics you want',
  'pl.rciamstatsviewer.database'        => 'Database',
  'pl.rciamstatsviewer.db_settings'     => 'Database Configuration',
  'pl.rciamstatsviewer.db_prefix'       => 'DB Prefix',
  'pl.rciamstatsviewer.pl_config'       => 'Statistics Configuration',
  'pl.rciamstatsviewer.summary'         => 'Summary',
  'pl.rciamstatsviewer.idp.pl'         => 'Identity Providers',
  'pl.rciamstatsviewer.sp.pl'          => 'Service Providers',
  'pl.rciamstatsviewer.idp_details.pl' => 'Identity Providers Details',
  'pl.rciamstatsviewer.sp_details.pl'  => 'Service Providers Details',


  //Database
  'rs.rciam_stats_viewer.error' => 'Save failed'
);
