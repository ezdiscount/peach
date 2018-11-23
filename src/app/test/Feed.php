<?php

namespace app\test;

class Feed
{
    const CAROUSEL = [
        [
            'thumb' => 'https://img.alicdn.com/tfscom/i2/1776843727/O1CN011dP0b4298OkOnaU_!!1776843727.jpg_640x0q85s150_.webp',
        ],
        [
            'thumb' => 'https://img.alicdn.com/tfscom/i1/4077026492/O1CN011xpNjELZ7CewZZ9_!!0-item_pic.jpg_640x0q85s150_.webp',
        ],
        [
            'thumb' => 'https://img.alicdn.com/tfscom/i4/193502143/O1CN011RhXEHsUfEvz24M_!!193502143.jpg_640x0q85s150_.webp',
        ],
    ];

    function carousel()
    {
        echo json_encode(self::CAROUSEL, JSON_UNESCAPED_UNICODE);
    }
}
