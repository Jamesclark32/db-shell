## What's the point? 

I've always preferred using direct command line clients for database access over web interfaces such as phpmyadmin.

In order to optimize that experience, I'm accustomed to using grcat and a custom terminal profile to colorize the output.

While this has given me the functionality I desire, it added a complexity in setting up a development environment as I needed to ensure the mysql client and grcat were available on the base machine and pull in custom terminal and grcat configurations. 

This package started as an experiment in capturing that functionality directly within the codebase, and eliminate those hurdles.

After using it for a few weeks, It feels to me that it successfully captures the interface, and that this package as a dev-dependency per project is cleaner than the environmental requirements I previously worked with. 