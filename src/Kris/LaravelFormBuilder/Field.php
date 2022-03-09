<?php

namespace Kris\LaravelFormBuilder;


class Field
{
    // Simple fields
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const SELECT = 'select';
    const CHOICE = 'choice';
    const CHECKBOX = 'checkbox';
    const RADIO = 'radio';
    const PASSWORD = 'password';
    const HIDDEN = 'hidden';
    const FILE = 'file';
    const STATIC = 'static';
    //Date time fields
    const DATE = 'date';
    const DATETIME_LOCAL = 'datetime-local';
    const MONTH = 'month';
    const TIME = 'time';
    const WEEK = 'week';
    //Special Purpose fields
    const COLOR = 'color';
    const SEARCH = 'search';
    const IMAGE = 'image';
    const EMAIL = 'email';
    const URL = 'url';
    const TEL = 'tel';
    const NUMBER = 'number';
    const RANGE = 'range';
    const ENTITY = 'entity';
    const FORM = 'form';
    //Buttons
    const BUTTON_SUBMIT = 'submit';
    const BUTTON_RESET = 'reset';
    const BUTTON_BUTTON = 'button';
}
