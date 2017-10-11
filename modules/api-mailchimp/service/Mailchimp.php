<?php
/**
 * Mailchimp service
 * @package api-mailchimp
 * @version 0.0.1
 * @upgrade true
 */

namespace ApiMailchimp\Service;
use DrewM\MailChimp\MailChimp as MC;
use DrewM\MailChimp\Batch as B;

class Mailchimp
{
    protected $mc;
    protected $list;
    
    public $last_error;
    
    private function check_error($result){
        if(isset($result['detail'])
        && isset($result['status'])
        && in_array($result['status'], [400, 404]))
            return !!($this->last_error = $result['detail']);
        return false;
    }
    
    private function format_member($member){
        return (object)[
            'id'     => $member['id'],
            'email'  => $member['email_address'],
            'status' => $member['status'],
            'name'   => trim(implode(' ', $member['merge_fields'])),
            'first_name' => $member['merge_fields']['FNAME'],
            'last_name'  => $member['merge_fields']['LNAME'],
            'created'=> $member['timestamp_opt']
        ];
    }
    
    public function __construct(){
        $this->list = \Phun::$dispatcher->setting->mailchimp_list_id;
        $this->mc = new MC(\Phun::$dispatcher->setting->mailchimp_app_key);
    }
    
    public function create($data){
        $mc = $this->mc->post('lists/' . $this->list . '/members', $data);
        
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        return $this->format_member($mc);
    }
    
    public function create_batch($rows){
        $b = $this->mc->new_batch();
        
        foreach($rows as $index => $row)
            $b->post('op'.$index, 'lists/' . $this->list . '/members', $row);
        
        $result = $b->execute();
        
        if($this->check_error($result))
            return false;
        return true;
    }
    
    public function get($cond, $rpp=12, $page=1){
        $page--;
        
        $cond['count'] = $rpp;
        $cond['offset']= $rpp * $page;
        $mc = $this->mc->get('lists/' . $this->list . '/members', $cond);
        
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        if($this->check_error($mc))
            return false;
        
        $result = (object)[
            'total' => $mc['total_items'],
            'items' => []
        ];
        
        foreach($mc['members'] as $email)
            $result->items[] = $this->format_member($email);
        
        return $result;
    }
    
    public function member($id){
        $mc = $this->mc->get('lists/' . $this->list . '/members/' . $id);
        
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        return $this->format_member($mc);
    }
    
    public function remove($id){
        $this->mc->delete('lists/' . $this->list . '/members/' . $id);
    }
    
    public function search($cond, $rpp=12, $page=1){
        $page--;
        
        $cond['count']   = $rpp;
        $cond['offset']  = $page * $rpp;
        $cond['list_id'] = $this->list;
        
        $mc = $this->mc->get('search-members', $cond);
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        $result = (object)[
            'total' => $mc['exact_matches']['total_items'] + $mc['full_search']['total_items'],
            'items' => []
        ];
        
        foreach($mc['exact_matches']['members'] as $email)
            $result->items[] = $this->format_member($email);
        
        foreach($mc['full_search']['members'] as $email)
            $result->items[] = $this->format_member($email);
            
        return $result;
    }
    
    public function stat(){
        $mc = $this->mc->get('lists/' . $this->list, ['fields'=>'stats']);
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        return $mc['stats']['member_count'];
    }
    
    public function update($id, $data){
        $mc = $this->mc->patch('lists/' . $this->list . '/members/' . $id, $data);
        
        if(!$mc){
            $this->last_error = $this->mc->getLastError();
            return false;
        }
        
        if($this->check_error($mc))
            return false;
        
        return $this->format_member($mc);
    }
}