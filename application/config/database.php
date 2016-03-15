<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
  'default' => array
  (
    'type'       => 'mysql',
    'connection' => array(
      /**
       * The following options are available for MySQL:
       *
       * string   hostname     server hostname, or socket
       * string   database     database name
       * string   username     database username
       * string   password     database password
       * boolean  persistent   use persistent connections?
       *
       * Ports and sockets may be appended to the hostname.
       */
      'hostname'   => '127.0.0.1',
      'database'   => 'rover',
      'username'   => 'root',
      'password'   => 'roverdbuser',
      'persistent' => FALSE,
    ),
    'table_prefix' => '',
    'charset'      => 'utf8',
    'caching'      => FALSE,
    'profiling'    => TRUE,
  ),
);
