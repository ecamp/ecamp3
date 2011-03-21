<?php
/**
 * This is the base class for both Panels and Plugins.
 * It shouldn't be extended by your own plugins - simply write a strategy!
 */

namespace Entity;

/**
 * @Entity
 * @Table(name="plugins")
 */
class Plugin extends BaseEntity {
	/**
	 * The id of the plugin item instance
	 * @var integer
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Event")
	 * @JoinColumn(nullable=true)
	 * TODO: set to nullable=false later
	 */
	public $event;

	/**
	 * This var contains the classname of the strategy
	 * that is used for this pluginitem. (This string (!) value will be persisted by Doctrine 2)
	 *
	 * @var string
	 * @Column(type="string", length=64, nullable=false )
	 */
	protected $strategyClassName;

	/**
	 * This var contains an instance of $this->pluginStrategy. Will not be persisted by Doctrine 2.
	 * The instance is loaded with a PostLoad event listener
	 *
	 * @var IPluginStrategy
	 */
	protected $strategyInstance;

	
	public function getId(){ return $this->id; }
	
	public function setEvent(Event $event){ $this->event = $event; }
	public function getEvent()            { return $this->event;   }
	
	
	/**
	 * Returns the strategy that is used for this pluginitem.
	 *
	 * The strategy itself defines how this plugin can be rendered etc.
	 *
	 * @return string
	 */
	public function getStrategyClassName() {
		return $this->strategyClassName;
	}

	/**
	 * Returns the instantiated strategy
	 *
	 * @return IPluginStrategy
	 */
	public function getStrategyInstance() {
		return $this->strategyInstance;
	}

	/**
	 * Sets the strategy this plugin / panel should work as. Make sure that you've used
	 * this method before persisting the plugin!
	 *
	 * @param IPluginStrategy $strategy
	 */
	public function setStrategy(\Plugin\IPluginStrategy $strategy) {
		$this->strategyInstance  = $strategy;
		$this->strategyClassName = get_class($strategy);
		$strategy->setPlugin($this);
	}
}