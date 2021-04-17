module.exports = {
  PRINT_SERVER: process.env.PRINT_SERVER || 'http://print:3003',
  SESSION_COOKIE_DOMAIN: process.env.SESSION_COOKIE_DOMAIN || 'print',
  SENTRY_WORKER_PRINT_PUPPETEER_DSN: process.env.SENTRY_WORKER_PRINT_PUPPETEER_DSN || '',
  AMQP_HOST: process.env.AMQP_HOST || 'rabbitmq',
  AMQP_PORT: process.env.AMQP_PORT || '5672',
  AMQP_VHOST: process.env.AMQP_VHOST || '/',
  AMQP_USER: process.env.AMQP_USER || 'guest',
  AMQP_PASS: process.env.AMQP_PASS || 'guest',
};
