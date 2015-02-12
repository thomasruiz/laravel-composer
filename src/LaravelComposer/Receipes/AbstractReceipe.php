<?php namespace LaravelComposer\Receipes;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class AbstractReceipe implements Receipe
{

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var QuestionHelper
     */
    protected $helper;

    /**
     * Construct a new VersionReceipe object
     *
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $this->helper = $helper;
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * Ask a question.
     *
     * @param string        $question
     * @param bool|string[] $choices
     * @param string        $default
     *
     * @return string
     */
    protected function ask($question, $choices = true, $default = null)
    {
        if (is_bool($choices)) {
            $question = new ConfirmationQuestion($question, $choices);
        } elseif (is_string($choices)) {
            $question = new Question($question, $choices);
        } else {
            $question = new ChoiceQuestion($question, $choices, $default);
        }

        return $this->helper->ask($this->input, $this->output, $question);
    }
}
