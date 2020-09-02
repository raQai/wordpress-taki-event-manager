<script>
  export let event;
  let getWeekDayName = (date) =>
    [
      "Sonntag",
      "Montag",
      "Dienstag",
      "Mittwoch",
      "Donnerstag",
      "Freitag",
      "Samstag",
      "Sonntag",
    ][date.getDay()];

  let getShortMonthName = (date) =>
    [
      "Jan",
      "Feb",
      "Mär",
      "Apr",
      "Mai",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Okt",
      "Nov",
      "Dez",
    ][date.getMonth()];

  let getDateString = (date) =>
    date
      ? `${getWeekDayName(date)}, ${Intl.DateTimeFormat("de-DE").format(date)}`
      : undefined;
  let getTimeString = (time) =>
    time ? time.toString().replace(":", ".") : undefined;

  let getStartToEnd = (start, end) => `${start}${end ? ` - ${end}` : ""}`;

  let getStartToEndDate = (start, end) =>
    getStartToEnd(getDateString(start), getDateString(end));

  let getStartToEndTime = (start, end) =>
    start
      ? `${getStartToEnd(getTimeString(start), getTimeString(end))} Uhr`
      : undefined;

  let dates = (function (event) {
    if (!event.biws__datetime_meta) {
      return {};
    }

    const startDate = event.biws__datetime_meta.datetime__start_date;
    const endDate = event.biws__datetime_meta.datetime__end_date;

    return {
      start: startDate ? new Date(Date.parse(startDate)) : undefined,
      end: endDate ? new Date(Date.parse(endDate)) : undefined,
    };
  })(event);

  let times = (function (event) {
    if (!event.biws__datetime_meta) {
      return {};
    }

    const startTime = event.biws__datetime_meta.datetime__start_time;
    const endTime = event.biws__datetime_meta.datetime__end_time;

    return {
      start: startTime,
      end: endTime,
    };
  })(event);

  let getLocations = (location) => {
    let output = [];
    output.push(location.name);
    output.push(location.building);
    output.push(
      [location.street, location.street_nr].filter(Boolean).join(" ")
    );
    output.push([location.zip, location.location].filter(Boolean).join(" "));
    return output.filter(Boolean).join(", $").split("$");
  };

  let lazyMail = async (node, data) => {
    setTimeout(() => {
      node.setAttribute("href", data.href);
    }, 2500);
  };
</script>

<style>
  :root {
    --biws-card-border-radius: 3px;
    --biws-red: #db3b0f;
    --biws-grey0: #fafafa;
    --biws-grey1: #eee;
    --biws-grey2: #e0e0e0;
  }

  .biws__divider {
    height: 1px;
    background-color: var(--biws-grey2);
  }

  .biws__event_item {
    word-break: break-word;
    border-radius: var(--biws-card-border-radius);
    margin: 0.5rem 0 1rem 0;
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);

    -webkit-transition: -webkit-box-shadow 0.25s;
    transition: -webkit-box-shadow 0.25s;
    transition: box-shadow 0.25s;
    transition: box-shadow 0.25s, -webkit-box-shadow 0.25s;
  }

  .biws__event_item:hover {
    -webkit-box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2),
      0 6px 20px 0 rgba(0, 0, 0, 0.19);
    box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2),
      0 6px 20px 0 rgba(0, 0, 0, 0.19);
  }

  .biws__event_item > * {
    padding: 1rem 4rem 2rem;
  }

  .biws__event_spacer {
    width: 12rem;
    margin-left: -4rem;
    margin-right: 2rem;
    flex-shrink: 0;
  }

  .biws__event_date {
    background-color: var(--biws-red);
    border-radius: 0 var(--biws-card-border-radius)
      var(--biws-card-border-radius) 0;
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);

    padding: 1.5rem;
    color: #fff;
    text-transform: uppercase;
    text-align: center;

    font-size: 2.5rem;
    line-height: 2.5rem;
  }

  .biws__event_planning {
    font-size: 1rem;
  }

  .biws__event_day {
    font-weight: bold;
  }

  .biws__event_content {
    padding-bottom: 1rem;
  }

  .biws__event_title {
    margin-top: 4rem;
    font-weight: 300;
    color: rgba(0, 0, 0, 0.8);
  }

  .biws__event_info {
    flex-grow: 1;
  }

  .biws__event_details {
    margin: 2rem 0;
  }

  .biws__event_details > div {
    display: flex;
    margin-bottom: 1rem;
  }

  .biws__event_details > div > * {
    padding-top: 0.4rem;
    display: block;
  }

  .biws__event_details p {
    margin-bottom: 1rem;
  }

  .biws__event_details p:last-of-type {
    margin-bottom: 0;
  }

  .biws__event_details p span {
    display: inline-block;
    padding-right: 0.5rem;
  }

  .biws__event_details svg {
    flex-shrink: 0;
    padding-right: 1rem;
  }

  .biws__event_footer {
    background-color: var(--biws-grey0);
    border-top: 1px solid var(--biws-grey1);
    border-radius: 0 0 var(--biws-card-border-radius)
      var(--biws-card-border-radius);
  }

  .biws__event_contacts {
    display:block;
  }

  .biws__contact_wrapper {
    margin: 1rem;
    flex-shrink: 0;
    flex-grow: 1;
  }

  .biws__contact_wrapper > * {
    display: block;
  }

  .biws__contact {
    font-weight: bold;
  }

  @media (min-width: 900px) {
    .biws__event_item > * {
      display: flex;
    }

    .biws__event_date {
      margin-bottom: 0;
    }

    .biws__event_date > * {
      display: block;
    }

    .biws__event_day {
      font-size: 5rem;
      line-height: 5rem;
    }

  .biws__event_contacts {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
  }

  .biws__event_info {
    margin-right:10rem;
  }

    .biws__event_contacts {
      margin: 0 9rem;
    }
  }
</style>

<div class="biws__event_item">
  <div class="biws__event_content">
    <div class="biws__event_spacer">
      <div class="biws__event_date">
        {#if dates.start}
          <span class="biws__event_day">{dates.start.getDate()}</span>
          <span class="biws__event_month">
            {getShortMonthName(dates.start)}
          </span>
        {:else}<span class="biws__event_planning">In Planung</span>{/if}
      </div>
    </div>
    <div class="biws__event_info">
      <h3 class="biws__event_title">
        {@html event.title}
      </h3>
      {#if event.content}
        <div class="biws__event_description">
          {@html event.content}
        </div>
      {/if}
      <div class="biws__divider" />
      <div class="biws__event_details">
        {#if dates.start}
          <div class="biws__event_details_item">
            <svg style="width:3rem;height:2.5rem" viewBox="0 0 24 24">
              <path
                fill="#888"
                d="M9,10H7V12H9V10M13,10H11V12H13V10M17,10H15V12H17V10M19,3H18V1H16V3H8V1H6V3H5C3.89,3
                3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0
                19,3M19,19H5V8H19V19Z" />
            </svg>
            <p>{getStartToEndDate(dates.start, dates.end)}</p>
          </div>
        {/if}
        {#if times.start}
          <div class="biws__event_details_item">
            <svg style="width:3rem;height:2.5rem" viewBox="0 0 24 24">
              <path
                fill="#888"
                d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0
                0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22
                2,17.5 2,12A10,10 0 0,1
                12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" />
            </svg>
            <p>{getStartToEndTime(times.start, times.end)}</p>
          </div>
        {/if}
        {#if event.biws__location_tax}
          <div class="biws__event_details_item">
            <svg style="width:3rem;height:2.5rem" viewBox="0 0 24 24">
              <path
                fill="#888"
                d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1
              14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22
              12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" />
            </svg>
            <div>
              {#each event.biws__location_tax as location}
                <p>
                  {#each getLocations(location) as elem}
                    <span>{elem}</span>
                  {/each}
                </p>
              {/each}
            </div>
          </div>
        {/if}
      </div>
    </div>
  </div>
  <div class="biws__event_footer">
    {#if event.biws__contact_tax}
      <div class="biws__event_contacts">
        {#each event.biws__contact_tax as contact}
          <div class="biws__contact_wrapper">
            {#if contact.name}
              <span class="biws__contact">{contact.name}</span>
            {/if}
            {#if contact.phone}
              <span class="biws__tel">{contact.phone}</span>
            {/if}
            {#if contact.email}
              <a
                class="biws__highlight"
                href="mail2"
                use:lazyMail={{ href: 'mailto:' + contact.email }}>{contact.email}</a>
            {/if}
          </div>
        {/each}
      </div>
    {/if}
  </div>
</div>
