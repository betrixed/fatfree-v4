<?php

/**
 * Simple formatted output of all included files 
 * during script call.
 */

namespace WC;

use stdClass;

class PhpStats {
    const SIZE_MB = 1024.0 * 1024.0;
    static public function time_msec($b, $a) : string {
        return number_format(($b - $a)*1000.0,2);
    }
    
    static public function mem_MB(int $bytes) : string {
        return number_format($bytes / self::SIZE_MB, 3);
    }
    static public function preTable(array $data): string {
        $out = '';
        $format = "%32s   %s\n";
        $line = "--------------------";
        foreach ($data as $key => $val) {
            if (!is_array($val)) {
                if (is_bool($val)) {
                    $out .= sprintf($format, $key, $val ? "true" : "false");
                } else {
                    $out .= sprintf($format, $key, $val);
                }
            } else {
                $out .= $line . $key  . " (" . count($val) . ") "
                       . $line . PHP_EOL;
                $out .= static::preTable($val);
            }
        }
        return $out;
    }

    static public function opcache(bool $details = false): string {
        $conf = opcache_get_configuration();
        $hit_count = 0;
        $miss_count = 0;
        $file_mem = 0;
        $files = get_included_files();

        $out = "<br><pre><br>";
        $out .= "PHP version " . PHP_VERSION . PHP_EOL;
        $opc = '';
        if ($conf) {
            if ($details) {
                $opc .= static::preTable($conf);
            }
            else {
                $keys = ['enable','enable_cli', 'use_cwd','memory_consumption'];
                $extract = [];
                $vals = $conf['directives'];
                $opc .= "Selected Opcache directives:\n";
                foreach($keys as $k) {
                    $fk = 'opcache.' . $k;
                    $extract[$k] = $vals[$fk];
                }
                $extract['memory_consumption'] = number_format($extract['memory_consumption']/self::SIZE_MB,3) . " MB";
                $opc .= static::preTable($extract);
            }
        } else {
            $opc .= "Opcache is disabled\n";
        }

        if ($conf) {
            $status = opcache_get_status(true);
            $cached = $status['scripts'];
        }
        if ($details) {
            $opc .= "\nScripts list\n";
            $opc .= "---------------------\n";
        }
        foreach ($files as $path) {

            $iscached = $conf ? opcache_is_script_cached($path) : false;

            if ($iscached) {
                $hit_count += 1;
                $fdata = $cached[$path];
                $file_mem += $fdata['memory_consumption'];
                if ($details) {
                    $opc .= static::preTable($cached[$path]);
                    $opc .= "---------------------\n";
                }
                
            } else {
                $miss_count += 1;
                $opc .= $path . "   not cached\n";
            }
        }
        $total = $hit_count + $miss_count;
        $out .= "Included files total $total, opcached = $hit_count\n";
        $out .= "File cache memory total = " . self::mem_MB($file_mem) . " MB\n";
        $out .= "Average file = " .  self::mem_MB($file_mem/$total) . " MB\n";
        $out .= $opc;
        $out .= "</pre><br>\n";
        return $out;
    }
    

    static public function end_stats(stdClass $obj) : string
    {
        $mem1 = (float) memory_get_peak_usage();
        $mem2 = (float) memory_get_peak_usage(true);
        $show_time = $obj->show_time ?? null;
        $out = '';
        if ($show_time) {
            $end_time = microtime(true);
            
           
            $start_time = $obj->start_time ?? $_SERVER['REQUEST_TIME_FLOAT'];
            
            $out .= "Times (ms) total: " . self::time_msec($end_time,$start_time);
            
            $model_time = $obj->model_time ?? false; 
            $route_time = $obj->route_time ?? false;        
            $render_time = $obj->render_time ?? false;

            if ($route_time) {
                $out .=  " setup: " . self::time_msec($route_time ,$start_time);
            }
            if ($model_time && $route_time) {
                $out .=  " route: " . self::time_msec($model_time, $route_time);
            }
            
            if ($render_time && $model_time) {
                $out .=  " model: " . self::time_msec($render_time, $model_time);
            }
            
            if ($render_time) {
                $out .=  " render: " . self::time_msec($end_time, $render_time);
            }
        }
        $out .= "<br> Memory (MB) emalloc: " . self::mem_MB($mem1) . " cap: " . self::mem_MB($mem2);
        return $out;
    }
}
