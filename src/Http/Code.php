<?php

declare(strict_types=1);

namespace Schnell\Http;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Code
{
    /**
     * @var int
     */
    public const CONTINUE = 100;

    /**
     * @var int
     */
    public const SWITCHING_PROTOCOLS = 101;

    /**
     * @var int
     */
    public const PROCESSING = 102;

    /**
     * @var int
     */
    public const EARLY_HINTS = 103;

    /**
     * @var int
     */
    public const OK = 200;

    /**
     * @var int
     */
    public const CREATED = 201;

    /**
     * @var int
     */
    public const ACCEPTED = 202;

    /**
     * @var int
     */
    public const NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * @var int
     */
    public const NO_CONTENT = 204;

    /**
     * @var int
     */
    public const RESET_CONTENT = 205;

    /**
     * @var int
     */
    public const PARTIAL_CONTENT = 206;

    /**
     * @var int
     */
    public const MULTI_STATUS = 207;

    /**
     * @var int
     */
    public const ALREADY_REPORTED = 208;

    /**
     * @var int
     */
    public const IM_USED = 226;

    /**
     * @var int
     */
    public const MULTIPLE_CHOICES = 300;

    /**
     * @var int
     */
    public const MOVED_PERMANENTLY = 301;

    /**
     * @var int
     */
    public const FOUND = 302;

    /**
     * @var int
     */
    public const SEE_OTHER = 303;

    /**
     * @var int
     */
    public const NOT_MODIFIED = 304;

    /**
     * @var int
     */
    public const USE_PROXY = 305;

    /**
     * @var int
     */
    public const TEMPORARY_REDIRECT = 307;

    /**
     * @var int
     */
    public const PERMANENT_REDIRECT = 308;

    /**
     * @var int
     */
    public const BAD_REQUEST = 400;

    /**
     * @var int
     */
    public const UNAUTHORIZED = 401;

    /**
     * @var int
     */
    public const PAYMENT_REQUIRED = 402;

    /**
     * @var int
     */
    public const FORBIDDEN = 403;

    /**
     * @var int
     */
    public const NOT_FOUND = 404;

    /**
     * @var int
     */
    public const METHOD_NOT_ALLOWED = 405;

    /**
     * @var int
     */
    public const NOT_ACCEPTABLE = 406;

    /**
     * @var int
     */
    public const PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * @var int
     */
    public const REQUEST_TIMEOUT = 408;

    /**
     * @var int
     */
    public const CONFLICT = 409;

    /**
     * @var int
     */
    public const GONE = 410;

    /**
     * @var int
     */
    public const LENGTH_REQUIRED = 411;

    /**
     * @var int
     */
    public const PRECONDITION_FAILED = 412;

    /**
     * @var int
     */
    public const CONTENT_TOO_LARGE = 413;

    /**
     * @var int
     */
    public const URI_TOO_LONG = 414;

    /**
     * @var int
     */
    public const UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * @var int
     */
    public const RANGE_NOT_SATISFIABLE = 416;

    /**
     * @var int
     */
    public const EXPECTATION_FAILED = 417;

    /**
     * @var int
     */
    public const IM_A_TEAPOT = 418;

    /**
     * @var int
     */
    public const MISDIRECTED_REQUEST = 421;

    /**
     * @var int
     */
    public const UNPROCESSABLE_ENTITY = 422;

    /**
     * @var int
     */
    public const LOCKED = 423;

    /**
     * @var int
     */
    public const FAILED_DEPENDENCY = 424;

    /**
     * @var int
     */
    public const TOO_EARLY = 425;

    /**
     * @var int
     */
    public const UPGRADE_REQUIRED = 426;

    /**
     * @var int
     */
    public const PRECONDITION_REQUIRED = 428;

    /**
     * @var int
     */
    public const TOO_MANY_REQUESTS = 429;

    /**
     * @var int
     */
    public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * @var int
     */
    public const UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    /**
     * @var int
     */
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * @var int
     */
    public const NOT_IMPLEMENTED = 501;

    /**
     * @var int
     */
    public const BAD_GATEWAY = 502;

    /**
     * @var int
     */
    public const SERVICE_UNAVAILABLE = 503;

    /**
     * @var int
     */
    public const GATEWAY_TIMEOUT = 504;

    /**
     * @var int
     */
    public const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * @var int
     */
    public const VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * @var int
     */
    public const INSUFFICIENT_STORAGE = 507;

    /**
     * @var int
     */
    public const LOOP_DETECTED = 508;

    /**
     * @var int
     */
    public const NOT_EXTENDED = 510;

    /**
     * @var int
     */
    public const NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @psalm-api
     *
     * @param int $code
     * @return string|null
     */
    public static function toString(int $code): string|null
    {
        switch ($code) {
            case self::CONTINUE:
                return "Continue";
            case self::SWITCHING_PROTOCOLS:
                return "Switching Protocols";
            case self::PROCESSING:
                return "Processing";
            case self::EARLY_HINTS:
                return "Early Hints";
            case self::OK:
                return "Ok";
            case self::CREATED:
                return "Created";
            case self::ACCEPTED:
                return "Accepted";
            case self::NON_AUTHORITATIVE_INFORMATION:
                return "Non Authoritative Information";
            case self::NO_CONTENT:
                return "No Content";
            case self::RESET_CONTENT:
                return "Reset Content";
            case self::PARTIAL_CONTENT:
                return "Partial Content";
            case self::MULTI_STATUS:
                return "Multi Status";
            case self::ALREADY_REPORTED:
                return "Already Reported";
            case self::IM_USED:
                return "IM Used";
            case self::MULTIPLE_CHOICES:
                return "Multiple Choices";
            case self::MOVED_PERMANENTLY:
                return "Moved Permanently";
            case self::FOUND:
                return "Found";
            case self::SEE_OTHER:
                return "See Other";
            case self::NOT_MODIFIED:
                return "Not Modified";
            case self::USE_PROXY:
                return "Use Proxy";
            case self::TEMPORARY_REDIRECT:
                return "Temporary Redirect";
            case self::PERMANENT_REDIRECT:
                return "Permanent Redirect";
            case self::BAD_REQUEST:
                return "Bad Request";
            case self::UNAUTHORIZED:
                return "Unauthorized";
            case self::PAYMENT_REQUIRED:
                return "Payment Required";
            case self::FORBIDDEN:
                return "Forbidden";
            case self::NOT_FOUND:
                return "Not Found";
            case self::METHOD_NOT_ALLOWED:
                return "Method Not Allowed";
            case self::NOT_ACCEPTABLE:
                return "Not Acceptable";
            case self::PROXY_AUTHENTICATION_REQUIRED:
                return "Proxy Authentication Required";
            case self::REQUEST_TIMEOUT:
                return "Request Timeout";
            case self::CONFLICT:
                return "Conflict";
            case self::GONE:
                return "Gone";
            case self::LENGTH_REQUIRED:
                return "Length Required";
            case self::PRECONDITION_FAILED:
                return "Precondition Failed";
            case self::CONTENT_TOO_LARGE:
                return "Content Too Large";
            case self::URI_TOO_LONG:
                return "URI Too Long";
            case self:: UNSUPPORTED_MEDIA_TYPE:
                return "Unsupported Media Type";
            case self::RANGE_NOT_SATISFIABLE:
                return "Range Not Satisfiable";
            case self::EXPECTATION_FAILED:
                return "Expectation Failed";
            case self::IM_A_TEAPOT:
                return "I'm a teapot";
            case self::MISDIRECTED_REQUEST:
                return "Misdirected Request";
            case self::UNPROCESSABLE_ENTITY:
                return "Unprocessable Entity";
            case self::LOCKED:
                return "Locked";
            case self::FAILED_DEPENDENCY:
                return "Failed Dependency";
            case self::TOO_EARLY:
                return "Too Early";
            case self::UPGRADE_REQUIRED:
                return "Upgrade Required";
            case self::PRECONDITION_REQUIRED:
                return "Precondition Required";
            case self::TOO_MANY_REQUESTS:
                return "Too Many Requests";
            case self::REQUEST_HEADER_FIELDS_TOO_LARGE:
                return "Request Header Fields Too Large";
            case self::UNAVAILABLE_FOR_LEGAL_REASONS:
                return "Unavailable For Legal Reasons";
            case self::INTERNAL_SERVER_ERROR:
                return "Internal Server Error";
            case self::NOT_IMPLEMENTED:
                return "Not Implemented";
            case self::BAD_GATEWAY:
                return "Bad Gateway";
            case self::SERVICE_UNAVAILABLE:
                return "Service Unavailable";
            case self::GATEWAY_TIMEOUT:
                return "Gateway Timeout";
            case self::HTTP_VERSION_NOT_SUPPORTED:
                return "HTTP Version Not Supported";
            case self::VARIANT_ALSO_NEGOTIATES:
                return "Variant Also Negotiates";
            case self::INSUFFICIENT_STORAGE:
                return "Insufficient Storage";
            case self::LOOP_DETECTED:
                return "Loop Detected";
            case self::NOT_EXTENDED:
                return "Not Extended";
            case self::NETWORK_AUTHENTICATION_REQUIRED:
                return "Network Authentication Required";
        }

        return null;
    }
}
