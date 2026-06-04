
<div class="checkout-card">
    <h2>Confirm Payment Details</h2>
      <input type="hidden" name="txn_order_id" value="<?php echo time(); ?>">
      <input type="hidden" name="txn_amount" value="250.00">

      <div class="form-group">
        <label>Buyer Name</label>
        <input type="text" name="txn_buyer_name" value="Jane Doe" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="txn_buyer_email" value="janedoe@example.com" required>
      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="txn_buyer_phone" value="60178910111" required>
      </div>

      <div class="payment-methods">
        <label>Select Payment Method</label>

        <label class="method-option">
          <input type="radio" name="payment_method" value="fpx" checked>
          <span>FPX Online Banking</span>
        </label>

        <label class="method-option">
          <input type="radio" name="payment_method" value="card">
          <span>Credit / Debit Card</span>
        </label>
      </div>

      <div class="price-box">
        <span>Total Payable:</span>
        <span>RM 250.00</span>
      </div>

      <button type="submit">Proceed to Payment</button>
  </div>

  <div class="tnc-section">
        <h4>Terms & Conditions</h4>
        
        <div class="tnc-scrollbox">
            <p>RESERVATION</p>
            <p>- To confirm your reservation, a booking deposit to be paid to the Company or its licensed representatives.</p>
            <p>- If reservation is made less than 45 days before departure, the full applicable tour fare is payable at the point of reservation.</p>
            <P>- Balance payment of the applicable tour fare shall be made no later than 45 days before departure. Failure to do so may result in your reservation being cancelled and deposits forfeited.</p>
            <p>- All payments can be made via bank transfer, cheque or cash.</p>
            <p>- Credit cards are accepted at certain licensed representatives with an administrative charge.</p>
            <p>TOUR FARE INCLUSIONS</p>
            <p>- Your tour fare includes all airfares, airport taxes, accommodation, entrance fees, meals and gratuities to drivers and tour managers as specified in the tour brochure.</p>
            <p>TOUR FARE EXCLUSIONS</p>
            <p>- Your tour fare excludes travel insurance, visa fees (if any), excess baggage charges, optional tour activities (if any) and all items of a personal nature.</p>
            <p>CHANGE OF TOUR DATE OR TOUR PACKAGE</p>
            <p>- At any time up to 45 days before departure, you can request to change your booking to another departure date or a different similar tour at no extra charges. Beyond this time frame, you are deemed to have cancelled your tour and the following cancellation charges apply.</p>
            <p>CANCELLATION CHARGES</p>
            <p>- A cancellation of booking at your request must be made in writing to avoid dispute on the timing of cancellation as different charges applies.</p>
            <p>- Failure to show up on departure date or denied boarding for whatever reason shall be deemed to a cancellation of tour at last minute. If you wish to rejoin the tour at your own costs, please inform the Company ahead with the understanding there is no refund for any unutilized services.</p>
            <p>TOUR CANCELLATION BY THE COMPANY</p>
            <p>- The confirmation of all tour departures are subject to minimum group size of 20 per departure.</p>
            <p>- If it becomes necessary for the Company to cancel any departure due to poor responses, all payments made to the Company will be refunded in full within 14 days of tour cancellation notice.</p>
            <p>- The safety of all tour members and tour managers is our paramount priority. Hence, the Company will abide by any travel prohibition/ advisory issued by the authorities to cancel any departure. However, "fear of travel" by any individual in the absence of such prohibition/ advisory will be subjected to normal cancellation charges.</p>
            <p>VALID TRAVEL DOCUMENTS</p>
            <p>- It is your responsibility to ensure your passport has at least 6 months validity from the date of the last departure point for home</p>
            <p>- It is your responsibility to obtain the necessary visa or health certificate (based on your nationality as required by the respective authorities of the countries visited during the tour.</p>
            <p>- The Company is not liable for any compensation or refund to you shall you be denied travelling due to the above non-compliances.</p>
            <p>TRAVEL INSURANCE</p>
            <p>We strongly advised you to purchase your preferred travel insurance coverage to minimize your losses due to enforced trip cancellation, medical and hospitalization costs, theft, baggage lost etc. We will be pleased to assist you on this on request.</p>
            <p>CHANGES TO THE ITINERARY</p>
            <p>- While we endeavor to deliver all services according to specifications as detailed in our tour brochures, the Company reserve the right to alter the itinerary due to unusual traffic conditions, adverse weather, natural disasters and any reasons beyond our control.</p>
            <p>45 days and above : Deposit forfeiture</p>
            <p>30 - 44 days : 50% of tour fare forfeited</p>
            <p> Below 30 days : 100% of tour fare forfeited</p>

        </div>