<?php

class Text
{
    static private $which = [
      1=>[
          'classroom'=>'oneCla',
          'subject'=>'oneCou'
      ],
        2=>[
            'classroom'=>'twoCla',
            'subject'=>'twoCou'
        ],
        3=>[
            'classroom'=>'threeCla',
            'subject'=>'threeCou'
        ],
        4=>[
            'classroom'=>'fourCla',
            'subject'=>'fourCou'
        ],
        5=>[
            'classroom'=>'fiveCla',
            'subject'=>'fiveCou'
        ],
        6=>[
            'classroom'=>'sixCla',
            'subject'=>'sixCou'
        ],
        7=>[
            'classroom'=>'sevenCla',
            'subject'=>'sevenCou'
        ],
        8=>[
            'classroom'=>'eightCla',
            'subject'=>'eightCou'
        ]
    ];
    static private $day = [
        1=>'一',
        2=>'二',
        3=>'三',
        4=>'四',
        5=>'五',
        6=>'六',
        7=>'日'
    ];
    static function index($xmlObj)
    {
        $content = trim($xmlObj->Content);
        preg_match('/^login:(\d{10})_([\S]*$)/', $content, $res);
        if ($res) {
            $setRes = Logic::setSchedule($res[1], $res[2], $xmlObj->FromUserName . '');
            if ($setRes) {
                $sendContent = '课表设置成功';
            } else {
                $sendContent = '课表设置失败，请检查学号和密码是否正确';
            }
            Tools::sendTextToUser($sendContent);
            return true;
        }

        if (preg_match('/^s:(\d*)$/', $content, $res)) {
            if (0 < $res[1] && $res[1] < 18) {
                self::getScheduleArr($res[1]);
                return true;
            }
        }
        return false;
    }

    static private function getScheduleArr($week)
    {
        $scheduleArr = Logic::getThisWeekSch($week);
        foreach ($scheduleArr as $key => $value) {
            $arr = [];
            foreach ($value as $k => $v){
                if($v['classroom'])
                {
                    $arr[self::$which[$k]['classroom']] = [
                        'value'=>$v['classroom']
                    ];
                    $arr[self::$which[$k]['subject']] = [
                        'value'=>$v['subject']
                    ];
                }
            }
            $arr['week'] = [
                'value'=>$week
            ];
            $arr['day']= [
                'value'=>self::$day[$key]
            ];
            Tools::sendSchedule($arr,Tools::$userOpenId.'');
        }
    }

}