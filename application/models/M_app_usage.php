<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_app_usage extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }

    public function get_app_usage_report($usage_date) {
        $sql = "
        SELECT
        CONCAT( DATE_FORMAT(start_interval, '%H:%i'), ' - ', DATE_FORMAT(end_interval, '%H:%i')
        ) AS interval_time,
    
        GROUP_CONCAT(
         CONCAT (app_name, ' (', total_duration, ')') 
        ORDER BY total_duration DESC SEPARATOR ', '
        ) AS apps_used,


         ROUND(
        SUM(CASE WHEN productivity_level = '2' THEN total_duration ELSE 0 END) / total_interval_time * 100, 2
        ) AS productive_percentage,

        ROUND(
        SUM(CASE WHEN productivity_level = '0' THEN total_duration ELSE 0 END) / total_interval_time * 100, 2
         ) AS unproductive_percentage,
         ROUND(
        SUM(CASE WHEN productivity_level = '1' THEN total_duration ELSE 0 END) / total_interval_time * 100, 2
         ) AS neutral_percentage
        FROM (

        SELECT
        FLOOR(TIME_TO_SEC(TIME(start_time)) / 300) * 300 AS interval_start_seconds,
        SEC_TO_TIME(FLOOR(TIME_TO_SEC(TIME(start_time)) / 300) * 300) AS start_interval,
        SEC_TO_TIME(FLOOR(TIME_TO_SEC(TIME(start_time)) / 300) * 300 + 300) AS end_interval,
        app_name,
        app_id,
        productivity_level,
        TIMESTAMPDIFF(SECOND, start_time, end_time) AS total_duration,
        300 AS total_interval_time
            FROM user_app_usage_1
            WHERE usage_date = '2024-04-08'
        ) AS app_intervals
        GROUP BY start_interval
        ORDER BY start_interval;

    ";
    

   
    
    $query = $this->db->query($sql, array($usage_date));
    return $query->result();
    }


    public function get_usage_for_date($date) {
        $this->db->where('usage_date', $date);
        $query = $this->db->get('user_app_usage_1');
        return $query->result_array();
        
    }

    public function calculate_intervals($usage_data) {
        $intervals = [];
        
        foreach ($usage_data as $entry) {
            $start_time = new DateTime($entry['start_time']);
            $end_time = new DateTime($entry['end_time']);
            
            $interval_start = clone $start_time;
            
            while ($interval_start < $end_time) {
                $interval_end = clone $interval_start;
                $interval_end->modify('+5 minutes');
                
                $interval_key = $interval_start->format('H:i') . '-' . $interval_end->format('H:i');
                
                if (!isset($intervals[$interval_key])) {
                    $intervals[$interval_key] = [
                        'start_time' => $interval_start->format('H:i'),
                        'end_time' => $interval_end->format('H:i'),
                        'apps' => []
                    ];
                }

                $app_name = $entry['app_name'];
                $duration = $entry['duration'];
                
                if (!isset($intervals[$interval_key]['apps'][$app_name])) {
                    $intervals[$interval_key]['apps'][$app_name] = 0;
                }
                
                $intervals[$interval_key]['apps'][$app_name] += $duration;
                
                $interval_start = clone $interval_end;
            }
        }
        
        return $intervals;
    }
}
