<?php

return [
    'title' => 'MCP server',
    'description' => 'Connect AI assistants such as Claude, ChatGPT or Cursor to :app through the Model Context Protocol (MCP). Your assistant can then look up projects and items, read the discussion and comment for you.',
    'disabled' => 'The MCP server is disabled, users can\'t see this page or connect. Enable it in the settings under "MCP".',

    'permissions-title' => 'What your assistant can do',
    'permissions' => 'The assistant acts as you. It sees exactly the projects, items and comments you can see on the roadmap, and comments are posted under your name. Only admins and employees can move items between boards.',

    'step-token-title' => '1. Create a token',
    'step-token' => 'Create a personal token in your profile. You can revoke it at any time, which disconnects every assistant that uses it.',
    'step-token-button' => 'Create a token',
    'step-token-login' => 'Log in to create a token',

    'step-connect-title' => '2. Connect your assistant',
    'step-connect' => 'Add the server to your AI client and send the token as a bearer token. Replace <code>YOUR_TOKEN</code> with the token you created.',
    'endpoint' => 'Endpoint',
    'claude-code' => 'Claude Code',
    'json-config' => 'Cursor, VS Code and other clients that support remote MCP servers',

    'step-use-title' => '3. Ask away',
    'step-use' => 'Ask your assistant things like "What is planned for the next release?", "Summarise the discussion on the dark mode request" or "Move the CSV export item to In progress".',

    'tools-title' => 'Available tools',
];
