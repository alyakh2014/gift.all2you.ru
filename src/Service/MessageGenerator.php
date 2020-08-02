<?php


namespace App\Service;


class MessageGenerator
{
    public function getHappyMessage()
    {
        $messages = [
            'Вы это сделали! УРРАААА!',
            'Отлично! Проверяйте почту, мы то всё получили!'
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}