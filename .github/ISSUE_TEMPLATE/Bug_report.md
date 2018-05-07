---
name: 🐛 Bug Report
about: If something isn't working as expected 🤔.

---

## Bug report

| Question    | Answer
| ------------| ---------------
| Box version | x.y.z <!-- (`php-scoper.phar -V` or `composer show | grep humbug/php-scoper`) -->
| PHP version | x.y.z <!-- (`php -v`) -->
| Platform with version | e.g. Ubuntu/Windows/MacOS
| Github Repo | - <!-- (if public) -->


<!--
Replace this comment with your issue description. Please complete the table above
with the correct information when relevant and include if relevant:
- The steps to reproduce your issue
- The exact command run
- Your configuration file

Also ensure the issue lies with the isolated code and not with shipping the code in a PHAR. Please
refer to the documentation (#Recommendations) for more details.

For general support, please use the #humbug Slack channel: https://symfony.com/slack-invite.
-->

<details>
 <summary>scoper.inc.php</summary>
 
 ```php
 <?php
 
 declare(strict_types=1);
 
 use Isolated\Symfony\Component\Finder\Finder;
 
 return [
 ];

 ```
</details>

<details>
 <summary>Output</summary>
 
 ```bash
 $ command
 > output
 ```
</details>