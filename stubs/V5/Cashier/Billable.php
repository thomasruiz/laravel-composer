<?php namespace __NAMESPACE__\Entities;

use Doctrine\ORM\Mapping as ORM;
use Laravel\Cashier\Billable as BillableTrait;

trait Billable
{

    use BillableTrait;

    /**
     * @ORM\Column(name="stripe_active", type="boolean")
     */
    private $stripeActive = false;

    /**
     * @ORM\Column(name="stripe_id", type="string", nullable=true)
     */
    private $stripeId;

    /**
     * @ORM\Column(name="stripe_subscription", type="string", nullable=true)
     */
    private $stripeSubscription;

    /**
     * @ORM\Column(name="stripe_plan", type="string", nullable=true, length=100)
     */
    private $stripePlan;

    /**
     * @ORM\Column(name="last_four", type="string", nullable=true, length=4)
     */
    private $lastFour;

    /**
     * @ORM\Column(name="trial_ends_at", type="datetime", nullable=true)
     */
    private $trialEndsAt;

    /**
     * @ORM\Column(name="subscription_ends_at", type="datetime", nullable=true)
     */
    private $subscriptionEndsAt;

    /**
     * Write the entity to persistent storage.
     *
     * @return void
     */
    public function saveBillableInstance()
    {
        \EntityManager::persist($this);
        \EntityManager::flush();
    }

    /**
     * Determine if billing requires a credit card up front.
     *
     * @return bool
     */
    public function requiresCardUpFront()
    {
        if (isset( $this->cardUpFront )) {
            return $this->cardUpFront;
        }

        return true;
    }

    /**
     * Set Stripe as inactive on the entity.
     *
     * @return \Laravel\Cashier\Contracts\Billable
     */
    public function deactivateStripe()
    {
        $this->setStripeIsActive(false);
        $this->stripeSubscription = null;

        return $this;
    }

    /**
     * Determine if the entity has a current Stripe subscription.
     *
     * @return bool
     */
    public function stripeIsActive()
    {
        return $this->stripeActive;
    }

    /**
     * Set whether the entity has a current Stripe subscription.
     *
     * @param  bool $active
     *
     * @return \Laravel\Cashier\Contracts\Billable
     */
    public function setStripeIsActive($active = true)
    {
        $this->stripeActive = $active;

        return $this;
    }

    /**
     * Deteremine if the entity has a Stripe customer ID.
     *
     * @return bool
     */
    public function hasStripeId()
    {
        return !is_null($this->stripeId);
    }

    /**
     * @return boolean
     */
    public function getStripeActive()
    {
        return $this->stripeActive;
    }

    /**
     * @param boolean $stripeActive
     */
    public function setStripeActive($stripeActive)
    {
        $this->stripeActive = $stripeActive;
    }

    /**
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }

    /**
     * @param string $stripeId
     */
    public function setStripeId($stripeId)
    {
        $this->stripeId = $stripeId;
    }

    /**
     * @return string
     */
    public function getStripeSubscription()
    {
        return $this->stripeSubscription;
    }

    /**
     * @param string $stripeSubscription
     */
    public function setStripeSubscription($stripeSubscription)
    {
        $this->stripeSubscription = $stripeSubscription;
    }

    /**
     * @return string
     */
    public function getStripePlan()
    {
        return $this->stripePlan;
    }

    /**
     * @param string $stripePlan
     */
    public function setStripePlan($stripePlan)
    {
        $this->stripePlan = $stripePlan;
    }

    /**
     * @return string
     */
    public function getLastFour()
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     */
    public function setLastFour($lastFour)
    {
        $this->lastFour = $lastFour;
    }

    /**
     * @return \DateTime
     */
    public function getTrialEndsAt()
    {
        return $this->trialEndsAt;
    }

    /**
     * @param \DateTime $trialEndsAt
     */
    public function setTrialEndsAt($trialEndsAt)
    {
        $this->trialEndsAt = $trialEndsAt;
    }

    /**
     * @return \DateTime
     */
    public function getSubscriptionEndsAt()
    {
        return $this->subscriptionEndsAt;
    }

    /**
     * @param \DateTime $subscriptionEndsAt
     */
    public function setSubscriptionEndsAt($subscriptionEndsAt)
    {
        $this->subscriptionEndsAt = $subscriptionEndsAt;
    }
}
