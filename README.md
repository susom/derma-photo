# Derma Avatar Photo App

External Modularizing the shazam JS from original iteration


            // const newfilePopUp = (() => {
            //     let initFunctions = [];
            //     return {
            //         init() {
            //             const fns = initFunctions;
            //             initFunctions = undefined;
            //             for (const fn of fns) {
            //                 try { fn(); } catch (e) { }
            //             }
            //         },
            //         addInitFunction(fn) {
            //             if (initFunctions) {
            //                 // Init hasn't run yet, remember it
            //                 initFunctions.push(fn);
            //             } else {
            //                 // `init` has already run, call it almost immediately
            //                 // but *asynchronously* (so the caller never sees the
            //                 // call synchronously)
            //                 setTimeout(fn, 0);
            //                 // Or: `Promise.resolve().then(() => fn());`
            //                 // (Not `.then(fn)` just to avoid passing it an argument)
            //             }
            //         }
            //     };
            // })();
