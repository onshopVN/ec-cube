<?php
namespace Customize\Event;

/**
 * @method string getLevel()
 * @method setLevel(string $level)
 * @method string getMessage()
 * @method setMessage(string $message)
 * @method array getContext()
 * @method setContext(array $context)
 */
class LoggerArgumentsEvent extends \Symfony\Component\EventDispatcher\Event implements EventInterface
{
    use \Customize\Entity\GetSetTrait;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $context;

    /**
     * LoggerArgumentsEvent constructor.
     *
     * @param $level
     * @param $message
     * @param array $context
     */
    public function __construct($level, $message, array $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public static function getName()
    {
        return 'eccube.logger.log.arguments';
    }
}
