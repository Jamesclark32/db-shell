# Changelog
## 1.0.8
- Iniitial support for handling multiple connections. Add 'connections' command to display all connections configured in laravel and switch between them. Ensure queries work as expected, although updating and deleting operations may still require work.

## 1.0.7
- Rerun last use query during a dropped connection recovery.

## 1.0.6
- Handle missing .mysql_history files sanely.

## 1.0.5
- Patch

## 1.0.4
- Patch to fix bug with database ping added in 1.0.3.

## 1.0.3
- Patch to ping database on every execution and reconnect if needed. This resolve need to restart command after long periods without usage.

## 1.0.2
- Patch to support null values

## 1.0.1
- Misc tweaks for package infrastruction. Introduction of style ci and gitattributes, flesh out gitignore, etc.

## 1.0.0
- initial release
