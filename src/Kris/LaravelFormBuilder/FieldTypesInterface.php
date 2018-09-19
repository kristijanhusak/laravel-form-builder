<?php

namespace Kris\LaravelFormBuilder;


interface FieldTypesInterface
{
    // FIELD fields
    const FIELD_TEXT = 'text';
    const FIELD_TEXTAREA = 'textarea';
    const FIELD_SELECT = 'select';
    const FIELD_CHOICE = 'choice';
    const FIELD_CHECKBOX = 'checkbox';
    const FIELD_RADIO = 'radio';
    const FIELD_PASSWORD = 'password';
    const FIELD_HIDDEN = 'hidden';
    const FIELD_FILE = 'file';
    const FIELD_STATIC = 'static';
    //Date time fields
    const FIELD_DATE = 'date';
    const FIELD_DATETIME_LOCAL = 'datetime_local';
    const FIELD_MONTH = 'month';
    const FIELD_TIME = 'time';
    const FIELD_WEEK = 'week';
    //Special Purpose fields
    const FIELD_COLOR = 'color';
    const FIELD_SEARCH = 'search';
    const FIELD_IMAGE = 'image';
    const FIELD_EMAIL = 'email';
    const FIELD_URL = 'url';
    const FIELD_TEL = 'tel';
    const FIELD_NUMBER = 'number';
    const FIELD_RANGE = 'range';
    //Buttons
    const BUTTON_SUBMIT = 'submit';
    const BUTTON_RESET = 'reset';
    const BUTTON_BUTTON = 'button';
}
