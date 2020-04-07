// pollyfill for require.context (Webpack feature) for test environment
import registerRequireContextHook from 'babel-plugin-require-context-hook/register';
registerRequireContextHook();