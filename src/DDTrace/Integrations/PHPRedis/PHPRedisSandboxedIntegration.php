<?php

namespace DDTrace\Integrations\PHPRedis;

use DDTrace\Integrations\SandboxedIntegration;
use DDTrace\SpanData;
use DDTrace\Tag;
use DDTrace\Type;

class PHPRedisSandboxedIntegration extends SandboxedIntegration
{
    const NAME = 'phpredis';

    const NOT_SET = '__DD_NOT_SET__';
    const CMD_MAX_LEN = 1000;
    const VALUE_TOO_LONG_MARK = '...';
    const VALUE_MAX_LEN = 100;
    const VALUE_PLACEHOLDER = "?";

    /**
     * @return string The integration name.
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Add instrumentation to PDO requests
     */
    public function init()
    {
        $integration = $this;

        $traceConnectOpen = function (SpanData $span, $args) {
            PHPRedisSandboxedIntegration::enrichSpan($span);
            $span->meta[Tag::TARGET_HOST] = (isset($args[0]) && \is_string($args[0])) ? $args[0] : '127.0.0.1';
            $span->meta[Tag::TARGET_PORT] = (isset($args[1]) && \is_numeric($args[1])) ? $args[1] : 6379;
        };
        \DDTrace\trace_method('Redis', 'connect', $traceConnectOpen);
        \DDTrace\trace_method('Redis', 'pconnect', $traceConnectOpen);
        \DDTrace\trace_method('Redis', 'open', $traceConnectOpen);
        \DDTrace\trace_method('Redis', 'popen', $traceConnectOpen);

        self::traceMethodNoArgs('close');
        self::traceMethodNoArgs('auth');
        self::traceMethodNoArgs('ping');
        self::traceMethodNoArgs('echo');
        self::traceMethodNoArgs('bgRewriteAOF');
        self::traceMethodNoArgs('bgSave');
        self::traceMethodNoArgs('flushAll');
        self::traceMethodNoArgs('flushDb');
        self::traceMethodNoArgs('save');
        // We do not trace arguments of restore as they are binary
        self::traceMethodNoArgs('restore');

        \DDTrace\trace_method('Redis', 'select', function (SpanData $span, $args) {
            PHPRedisSandboxedIntegration::enrichSpan($span);
            if (isset($args[0]) && \is_numeric($args[0])) {
                $span->meta['db.index'] = $args[0];
            }
        });

        self::traceMethodAsCommand('append');
        self::traceMethodAsCommand('decr');
        self::traceMethodAsCommand('decrBy');
        self::traceMethodAsCommand('get');
        self::traceMethodAsCommand('getBit');
        self::traceMethodAsCommand('getRange');
        self::traceMethodAsCommand('getSet');
        self::traceMethodAsCommand('incr');
        self::traceMethodAsCommand('incrBy');
        self::traceMethodAsCommand('incrByFloat');
        self::traceMethodAsCommand('mGet');
        self::traceMethodAsCommand('getMultiple');
        self::traceMethodAsCommand('mSet');
        self::traceMethodAsCommand('mSetNx');
        self::traceMethodAsCommand('set');
        self::traceMethodAsCommand('setBit');
        self::traceMethodAsCommand('setEx');
        self::traceMethodAsCommand('pSetEx');
        self::traceMethodAsCommand('setNx');
        self::traceMethodAsCommand('setRange');
        self::traceMethodAsCommand('strLen');

        self::traceMethodAsCommand('del');
        self::traceMethodAsCommand('delete');
        self::traceMethodAsCommand('dump');
        self::traceMethodAsCommand('exists');
        self::traceMethodAsCommand('keys');
        self::traceMethodAsCommand('getKeys');
        self::traceMethodAsCommand('scan');
        self::traceMethodAsCommand('migrate');
        self::traceMethodAsCommand('move');
        self::traceMethodAsCommand('persist');
        self::traceMethodAsCommand('rename');
        self::traceMethodAsCommand('object');
        self::traceMethodAsCommand('randomKey');
        self::traceMethodAsCommand('renameKey');
        self::traceMethodAsCommand('renameNx');
        self::traceMethodAsCommand('type');
        self::traceMethodAsCommand('sort');
        self::traceMethodAsCommand('expire');
        self::traceMethodAsCommand('expireAt');
        self::traceMethodAsCommand('setTimeout');
        self::traceMethodAsCommand('pexpire');
        self::traceMethodAsCommand('pexpireAt');
        self::traceMethodAsCommand('ttl');
        self::traceMethodAsCommand('pttl');

        // Hash functions
        self::traceMethodAsCommand('hDel');
        self::traceMethodAsCommand('hExists');
        self::traceMethodAsCommand('hGet');
        self::traceMethodAsCommand('hGetAll');
        self::traceMethodAsCommand('hIncrBy');
        self::traceMethodAsCommand('hIncrByFloat');
        self::traceMethodAsCommand('hKeys');
        self::traceMethodAsCommand('hLen');
        self::traceMethodAsCommand('hMGet');
        self::traceMethodAsCommand('hMSet');
        self::traceMethodAsCommand('hSet');
        self::traceMethodAsCommand('hSetNx');
        self::traceMethodAsCommand('hVals');
        self::traceMethodAsCommand('hScan');
        self::traceMethodAsCommand('hStrLen');

        // Lists
        self::traceMethodAsCommand('blPop');
        self::traceMethodAsCommand('brPop');
        self::traceMethodAsCommand('bRPopLPush');
        self::traceMethodAsCommand('lGet');
        self::traceMethodAsCommand('lGetRange');
        self::traceMethodAsCommand('lIndex');
        self::traceMethodAsCommand('lInsert');
        self::traceMethodAsCommand('listTrim');
        self::traceMethodAsCommand('lLen');
        self::traceMethodAsCommand('lPop');
        self::traceMethodAsCommand('lPush');
        self::traceMethodAsCommand('lPushx');
        self::traceMethodAsCommand('lRange');
        self::traceMethodAsCommand('lRem');
        self::traceMethodAsCommand('lRemove');
        self::traceMethodAsCommand('lSet');
        self::traceMethodAsCommand('lSize');
        self::traceMethodAsCommand('lTrim');
        self::traceMethodAsCommand('rPop');
        self::traceMethodAsCommand('rPopLPush');
        self::traceMethodAsCommand('rPush');
        self::traceMethodAsCommand('rPushX');

        // Sets
        self::traceMethodAsCommand('sAdd');
        self::traceMethodAsCommand('sCard');
        self::traceMethodAsCommand('sContains');
        self::traceMethodAsCommand('sDiff');
        self::traceMethodAsCommand('sDiffStore');
        self::traceMethodAsCommand('sGetMembers');
        self::traceMethodAsCommand('sInter');
        self::traceMethodAsCommand('sInterStore');
        self::traceMethodAsCommand('sIsMember');
        self::traceMethodAsCommand('sMembers');
        self::traceMethodAsCommand('sMove');
        self::traceMethodAsCommand('sPop');
        self::traceMethodAsCommand('sRandMember');
        self::traceMethodAsCommand('sRem');
        self::traceMethodAsCommand('sRemove');
        self::traceMethodAsCommand('sScan');
        self::traceMethodAsCommand('sSize');
        self::traceMethodAsCommand('sUnion');
        self::traceMethodAsCommand('sUnionStore');

        // Sorted Sets
        self::traceMethodAsCommand('zAdd');
        self::traceMethodAsCommand('zCard');
        self::traceMethodAsCommand('zSize');
        self::traceMethodAsCommand('zCount');
        self::traceMethodAsCommand('zIncrBy');
        self::traceMethodAsCommand('zInter');
        self::traceMethodAsCommand('zRange');
        self::traceMethodAsCommand('zRangeByScore');
        self::traceMethodAsCommand('zRevRangeByScore');
        self::traceMethodAsCommand('zRangeByLex');
        self::traceMethodAsCommand('zRank');
        self::traceMethodAsCommand('zRevRank');
        self::traceMethodAsCommand('zRem');
        self::traceMethodAsCommand('zDelete');
        self::traceMethodAsCommand('zRemRangeByRank');
        self::traceMethodAsCommand('zDeleteRangeByRank');
        self::traceMethodAsCommand('zRemRangeByScore');
        self::traceMethodAsCommand('zDeleteRangeByScore');
        self::traceMethodAsCommand('zRevRange');
        self::traceMethodAsCommand('zScore');
        self::traceMethodAsCommand('zUnion');
        self::traceMethodAsCommand('zScan');

        // Publish: we only trace publish because subscribe is blocking and it will have to be manually traced
        // as in long running processes.
        self::traceMethodAsCommand('publish');

        // Raw command
        self::traceMethodAsCommand('rawCommand');

        return SandboxedIntegration::LOADED;
    }

    public static function enrichSpan(SpanData $span, $method = null)
    {
        $span->service = 'phpredis';
        $span->type = Type::REDIS;
        if (null !== $method) {
            // method names for internal functions are lowered so we need to explitly set them if we want to have the
            // proper case.
            $span->name = $span->resource = "Redis.$method";
        }
    }

    public static function traceMethodNoArgs($method)
    {
        \DDTrace\trace_method('Redis', $method, function (SpanData $span, $args) use ($method) {
            PHPRedisSandboxedIntegration::enrichSpan($span, $method);
        });
    }

    public static function traceMethodAsCommand($method)
    {
        \DDTrace\trace_method('Redis', $method, function (SpanData $span, $args) use ($method) {
            PHPRedisSandboxedIntegration::enrichSpan($span, $method);
            // Obfuscable methods: see https://github.com/DataDog/datadog-agent/blob/master/pkg/trace/obfuscate/redis.go
            $span->meta[Tag::REDIS_RAW_COMMAND] = $method . ' ' . PHPRedisSandboxedIntegration::normalizeArgs($args);
        });
    }

    /**
     * Based on logic from python tracer:
     * https://github.com/DataDog/dd-trace-py/blob/0d7e7cb38216acb0c8b29f0ae1318d25bc160123/ddtrace/contrib/redis/util.py#L25
     *
     * @param array $args
     * @return string
     */
    public static function normalizeArgs($args)
    {
        $rawCommandParts = [];

        $totalArgsLength = 0;
        foreach ($args as $arg) {
            if ($totalArgsLength > self::CMD_MAX_LEN) {
                break;
            }

            $partValue = null;

            if (\is_string($arg)) {
                $partValue = $arg;
            } elseif (\is_numeric($arg)) {
                $partValue = (string)$arg;
            } elseif (\is_null($arg)) {
                $partValue = 'null';
            } elseif (\is_bool($arg)) {
                $partValue = $args ? 'true' : false;
            } elseif (\is_array($arg)) {
                // This is best effort as specific index might be missing or be shifted, e.g. [0 => 'a', 2 => 'b'].
                // In this case the worst that can happen is that we generate '0 a 2 b' instead of 'a b'. We accept this
                // in order to keep things as simple as possible.
                $isAssociative = array_values($arg) !== $arg;
                if ($isAssociative) {
                    foreach ($arg as $key => $val) {
                        $rawCommandParts[] = $key;
                        $rawCommandParts[] = self::normalizeArgs([ $val ]);
                    }
                } else {
                    $rawCommandParts[] = self::normalizeArgs($arg);
                }
                continue;
            } else {
                $rawCommandParts[] = self::VALUE_PLACEHOLDER;
                continue;
            }

            $len = strlen($partValue);
            if ($len > self::VALUE_MAX_LEN) {
                $partValue = substr($partValue, 0, self::VALUE_MAX_LEN) . self::VALUE_TOO_LONG_MARK;
            }
            if ($totalArgsLength + $len > self::CMD_MAX_LEN) {
                $partValue = substr($partValue, 0, self::CMD_MAX_LEN) . self::VALUE_TOO_LONG_MARK;
            }

            $rawCommandParts[] = $partValue;
            $totalArgsLength += strlen($partValue);
        }

        return \implode(' ', $rawCommandParts);
    }
}
