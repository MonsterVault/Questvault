window.onload = function () {
    // Get the current URL's query parameters
    const urlParams = new URLSearchParams(window.location.search);

    // Get the referral ID from the query string
    const referralId = urlParams.get('ref');

    // If the referral ID exists in the current URL, update the links
    if (referralId) {
        // Update the Party link with the referral ID
        const partyLink = document.querySelector("a[href='party.php']");
        if (partyLink) {
            partyLink.href = `party.php?ref=${encodeURIComponent(referralId)}`;
        }

        // Update the Main link (Items) with the referral ID
        const itemsLink = document.querySelector("a[href='Main.php']");
        if (itemsLink) {
            itemsLink.href = `Main.php?ref=${encodeURIComponent(referralId)}`;
        }
    }

    // Get the 'Buy' button and modal elements
    const buyButtons = document.querySelectorAll('.btn-buy'); // For all buy buttons
    const buyModal = document.getElementById('myModal'); // The modal for buying items
    const confirmBuyButton = document.getElementById('confirmBuy');
    const cancelBuyButton = document.getElementById('cancelBuy');

    // Show modal when any 'Buy' button is clicked
    buyButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            buyModal.style.display = 'flex'; // Show the modal
        });
    });

    // Hide modal when 'Cancel' button is clicked
    cancelBuyButton.addEventListener('click', function () {
        buyModal.style.display = 'none'; // Hide the modal
    });

    // Confirm the purchase when 'Confirm' button is clicked
    confirmBuyButton.addEventListener('click', function () {
        alert('You have successfully bought the item!');
        buyModal.style.display = 'none'; // Hide the modal after purchase
    });
};

// Function to close the modal
function closeModal() {
    const buyModal = document.getElementById('myModal');
    buyModal.style.display = 'none'; // Hide the modal
}
